<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Rental, Payment, Invoice};
use Illuminate\Support\Facades\{DB, Http, Log};
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PaymentController extends Controller
{
    private function getMerchantCode()
{
    return env('DUITKU_MERCHANT_CODE');
}

private function getApiKey()
{
    return env('DUITKU_API_KEY');
}

private function getCallbackUrl()
{
    return env('DUITKU_CALLBACK_URL');
}

    /* --------------------------------------------------------------------------
     | 🔹 STEP 1: Tampilkan detail sewa & buat transaksi utama
     * -------------------------------------------------------------------------- */
    public function detailRental($rental_id)
    {
        $rental = Rental::with(['car.brand', 'car.capacity', 'user'])->findOrFail($rental_id);
        abort_if($rental->user_id !== auth()->id(), 403);

        $pending = Payment::where('rental_id', $rental->rental_id)
            ->where('status_pembayaran', 'pending')->latest()->first();

        if ($pending) return redirect()->route('user.payments.process', $pending->payment_id);

        return view('user.payments.detail', compact('rental'));
    }

    /* --------------------------------------------------------------------------
     | ▶️ STEP 2: Kirim ke Duitku & redirect ke halaman pembayaran
     * -------------------------------------------------------------------------- */
  public function startProcess(Request $r, $rental_id)
{
    $r->validate(['metode' => 'required|in:qris,bca,bri']);
    $rental = Rental::with(['car.brand', 'user'])->findOrFail($rental_id);
    abort_if($rental->user_id !== auth()->id(), 403);

    // ✅ Ambil config dari .env
    $merchantCode = $this->getMerchantCode();
    $apiKey       = $this->getApiKey();
    $callbackUrl  = $this->getCallbackUrl();

    // 🚫 Jika sudah ada payment pending, arahkan ke sana
    if (Payment::where('rental_id', $rental->rental_id)->where('status_pembayaran', 'pending')->exists()) {
        $exist = Payment::where('rental_id', $rental->rental_id)->latest()->first();
        return redirect()->route('user.payments.process', $exist->payment_id);
    }

    $tempOrder = 'TMP-' . strtoupper(uniqid());

    $payload = [
        "merchantCode" => $merchantCode,
        "paymentAmount" => (int)$rental->total_biaya,
        "paymentMethod" => match($r->metode) {
            'bca' => 'BC', 'bri' => 'BR', default => 'QRIS',
        },
        "merchantOrderId" => $tempOrder,
        "productDetails" => "Sewa Mobil " . ($rental->car->brand->nama_merek ?? ''),
        "email" => $rental->user->email,
        "phoneNumber" => $rental->user->no_hp ?? '08123456789',
        "customerVaName" => $rental->user->nama_lengkap,
        "callbackUrl" => $callbackUrl,
        "returnUrl" => route('user.payments.index'),
        "signature" => md5($merchantCode . $tempOrder . (int)$rental->total_biaya . $apiKey),
        "expiryPeriod" => 30,
    ];

    $res = Http::post('https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry', $payload)->json();

    if (empty($res['paymentUrl'])) {
        Log::error('🚨 Duitku gagal buat transaksi', [
            'payload' => $payload,
            'response' => $res,
        ]);
        return back()->with('error', 'Gagal membuat transaksi, silakan coba lagi.');
    }

    // ✅ Update rental status
    $rental->update([
        'status_rental' => 'menunggu_pembayaran',
        'updated_at' => now(),
    ]);

    Payment::create([
        'rental_id' => $rental->rental_id,
        'gateway' => 'Duitku',
        'metode' => $r->metode,
        'payment_type' => 'main',
        'total_bayar' => $rental->total_biaya,
        'status_pembayaran' => 'pending',
        'gateway_reference' => $res['reference'] ?? ('MAN-' . rand(100000, 999999)),
        'payment_token' => $res['paymentUrl'],
        'merchant_order_id' => $tempOrder,
        'callback_status' => 'waiting',
        'tanggal_bayar' => now(),
        'expired_at' => now()->addMinutes(30),
    ]);

    return redirect()->away($res['paymentUrl']);
}


    private function duitkuPayload($rental, $payment, $metode)
    {
        $map = ['qris' => 'QRIS', 'bca' => 'BC', 'bri' => 'BR'];
        $amount = (int)$rental->total_biaya;
        $orderId = 'ORDER-' . $payment->payment_id;
        return [
            "merchantCode" => $this->getMerchantCode(),
            "paymentAmount" => $amount,
            "paymentMethod" => $map[$metode] ?? 'QRIS',
            "merchantOrderId" => $orderId,
            "productDetails" => "Sewa Mobil " . ($rental->car->brand->nama_merek ?? ''),
            "email" => $rental->user->email,
            "phoneNumber" => $rental->user->no_hp ?? '08123456789',
            "customerVaName" => $rental->user->nama_lengkap,
            "callbackUrl" => $this->getCallbackUrl(),
            "returnUrl" => route('user.payments.index'),
            "signature" => md5($this->getMerchantCode() . $orderId . $amount . $this->getApiKey()),
            "expiryPeriod" => 30,
        ];
    }

    /* --------------------------------------------------------------------------
     | 🕐 STEP 3: Halaman proses countdown
     * -------------------------------------------------------------------------- */
    public function process($id)
    {
        $payment = Payment::with(['rental.car.brand', 'rental.user'])->findOrFail($id);
        abort_if($payment->rental->user_id !== auth()->id(), 403);
        $this->syncFromAdmin($payment);

        if ($payment->status_pembayaran !== 'pending')
            return redirect()->route('user.payments.index')->with('error', 'Transaksi tidak aktif.');

        return view('user.payments.process', compact('payment'));
    }

    /* --------------------------------------------------------------------------
     | ❌ Cancel manual user
     * -------------------------------------------------------------------------- */
    public function cancelSoft($id)
    {
        $p = Payment::findOrFail($id);
        abort_if($p->rental->user_id !== auth()->id(), 403);

        $p->update(['status_pembayaran' => 'failed', 'callback_status' => 'cancelled']);
        $p->rental?->update(['status_rental' => 'dibatalkan']);
        Invoice::where('rental_id', $p->rental_id)->first()?->update(['status_invoice' => 'dibatalkan']);

        return back()->with('success', '❌ Pembayaran dibatalkan.');
    }

    /* --------------------------------------------------------------------------
     | 📋 Daftar pembayaran (index)
     * -------------------------------------------------------------------------- */
public function index(Request $r)
{
    $q = Payment::with(['rental.car.brand', 'rental.invoice'])
        ->where(function ($query) {
            $query->whereHas('rental', function ($rental) {
                $rental->where('user_id', auth()->id());
            })
            ->orWhereNull('rental_id'); // 🔹 Tambahan: tampilkan juga jika rental_id null
        });

    if ($r->filled('no_transaksi'))
        $q->whereHas('rental.invoice', fn($i) => 
            $i->where('invoice_id', str_replace('INV', '', $r->no_transaksi))
        );

    if ($r->filled('tanggal'))
        $q->whereDate('created_at', $r->tanggal);

    if ($r->filled('status'))
        $q->where('status_pembayaran', $r->status);

    $payments = $q->latest()->get();
    return view('user.payments.index', compact('payments'));
}


    /* --------------------------------------------------------------------------
     | ⏱️ Auto expire + sinkron cancel
     * -------------------------------------------------------------------------- */
    public function checkExpired()
{
    $now = now();

    // 1️⃣ Ambil semua rental_id dari invoice yang sudah dibatalkan / cancel manual
    $cancelled = Invoice::whereIn('status_invoice', ['cancel', 'dibatalkan'])
        ->pluck('rental_id');

    // 2️⃣ Ambil semua payment pending yang sudah expired atau invoice-nya dibatalkan
    $expired = Payment::where('status_pembayaran', 'pending')
        ->where(function ($q) use ($now, $cancelled) {
            $q->where('expired_at', '<=', $now)
              ->orWhereIn('rental_id', $cancelled);
        })
        ->get();

    foreach ($expired as $p) {
        // 3️⃣ Update payment ke failed
        $p->update([
            'status_pembayaran' => 'failed',
            'callback_status'   => 'expired',
        ]);

        // 4️⃣ Sinkron ke invoice (kalau ada)
        $invoice = Invoice::where('rental_id', $p->rental_id)->first();
        if ($invoice && $invoice->status_invoice === 'pending') {
            $invoice->update(['status_invoice' => 'dibatalkan']);
        }

        // 5️⃣ Sinkron ke rental (jika belum selesai)
        if ($p->rental && !in_array($p->rental->status_rental, ['selesai', 'selesai_dengan_charge'])) {
            $p->rental->update(['status_rental' => 'dibatalkan']);
        }

        Log::info("⏰ Payment expired otomatis:", [
            'payment_id' => $p->payment_id,
            'rental_id'  => $p->rental_id,
            'type'       => $p->payment_type,
        ]);
    }

    return response()->json([
        'message' => 'Synced',
        'count'   => $expired->count(),
    ]);
}


    /* --------------------------------------------------------------------------
     | 🔁 Auto-refresh: ambil status terbaru semua pembayaran
     * -------------------------------------------------------------------------- */
    public function statusList()
    {
        return response()->json(
            Payment::select('payment_id', 'status_pembayaran')->whereHas('rental', fn($q) => $q->where('user_id', auth()->id()))->get()
        );
    }

    /* --------------------------------------------------------------------------
     | 🟢 Callback dari Duitku
     * -------------------------------------------------------------------------- */
    public function callback(Request $r)
{
    Log::info('📥 CALLBACK MASUK', $r->all());

    // 🧾 Validasi Signature dari Duitku (lebih toleran format amount)
    $merchantCode = $r->merchantCode ?? '';
    $merchantOrderId = $r->merchantOrderId ?? '';
    $amountRaw = $r->amount ?? '';
    $amountClean = preg_replace('/[^0-9]/', '', $amountRaw); // hapus titik/koma
    $apiKey = $this->getApiKey();

    $expectedSign = md5($merchantCode . $amountClean . $merchantOrderId . $apiKey);
    $receivedSign = $r->signature ?? '';

    if ($expectedSign !== $receivedSign) {
        Log::error('⚠ Invalid signature', [
            'expected' => $expectedSign,
            'got' => $receivedSign,
            'raw_amount' => $amountRaw,
            'cleaned' => $amountClean,
            'merchantOrderId' => $merchantOrderId,
        ]);
        return response()->json(['message' => 'Invalid signature'], 400);
    }

    $p = Payment::where('merchant_order_id', $r->merchantOrderId)
        ->orWhere('gateway_reference', $r->reference)
        ->first();

    if (!$p) {
        Log::error('❌ Payment not found', ['merchantOrderId' => $r->merchantOrderId]);
        return response()->json(['message' => 'Payment not found'], 404);
    }

    $resultCode = $r->resultCode ?? '';
    $status = match ($resultCode) {
        '00' => 'success',
        '01', '02', '03' => 'pending',
        default => 'failed',
    };

    Log::info('✅ Payment ditemukan', [
        'id' => $p->payment_id,
        'before' => $p->status_pembayaran,
        'status' => $status,
    ]);

    $updated = $p->update([
        'status_pembayaran' => $status,
        'callback_status' => $status === 'success' ? 'done' : ($status === 'failed' ? 'error' : 'waiting'),
        'tanggal_bayar' => now(),
    ]);

    Log::info('💾 Hasil update payment', ['updated' => $updated]);

    if ($updated) {
        $p->rental?->update([
            'status_rental' => $status === 'success'
                ? 'berjalan'
                : ($status === 'failed' ? 'dibatalkan' : 'menunggu_pembayaran'),
        ]);
        Log::info('🚗 Rental ikut diupdate', ['rental_id' => $p->rental_id]);
    }

     // ✅ WhatsApp Notification & Invoice Auto-Send
if ($status === 'success' && $p->rental) {
    $user = $p->rental->user;
    $car  = $p->rental->car;

    $mulai   = Carbon::parse($p->rental->tanggal_mulai)->format('d M Y H:i');
    $selesai = Carbon::parse($p->rental->tanggal_selesai)->format('d M Y H:i');

    // ✅ Generate & save PDF invoice
    $pdfPath = storage_path("app/public/Kuitansi-{$p->payment_id}.pdf");
    Pdf::loadView('user.payments.receipt_pdf', ['payment' => $p])->save($pdfPath);

    // ✅ URL file untuk dikirim via WA
    $pdfUrl = url("storage/Kuitansi-{$p->payment_id}.pdf");

    /* -------------------------
       1) Notice Pembayaran Berhasil
    --------------------------*/
    \App\Helpers\Whatsapp::send(
        $user->no_hp,
        "*Pembayaran Berhasil*

Halo {$user->nama_lengkap}, terima kasih telah melakukan pembayaran rental mobil.

• Order ID: {$p->payment_id}
• Mobil: {$car->brand->nama_merek} {$car->model}
• Jadwal: {$mulai} — {$selesai}
• Status: Lunas

Invoice digital Anda sudah kami siapkan. Silakan lihat link berikut setelah pesan ini."
    );

    /* -------------------------
       2) Info Pickup / Delivery
    --------------------------*/
    if ($p->rental->metode_pickup == 'ambil_sendiri') {

        \App\Helpers\Whatsapp::send(
            $user->no_hp,
            "*Informasi Pengambilan Mobil*

Silakan mengambil kendaraan di kantor kami:

HexaRent  
Jl. Abdul Halim No.128, Cimahi Tengah  
Google Maps: https://maps.app.goo.gl/2Yw3YymwUTG1KCLQ7

Waktu pengambilan:
{$mulai} WIB

Mohon membawa KTP dan menunjukkan bukti pemesanan."
        );

    } else {

        \App\Helpers\Whatsapp::send(
            $user->no_hp,
            "*Pengantaran Mobil*

Kendaraan akan diantar ke alamat Anda sesuai jadwal.

• Estimasi tiba: {$mulai} WIB
• Driver akan menghubungi Anda sebelum keberangkatan.

Terima kasih telah memilih layanan kami."
        );
    }

    /* -------------------------
       3) Link Invoice PDF
    --------------------------*/
    \App\Helpers\Whatsapp::send(
        $user->no_hp,
        "*Invoice & Bukti Pembayaran*

File invoice digital Anda siap diunduh:
{$pdfUrl}

Jika ada pertanyaan, kami siap membantu kapan saja."
    );

    /* -------------------------
       4) Notifikasi Admin
    --------------------------*/
    \App\Helpers\Whatsapp::send(
        env('ADMIN_WA'),
        "*Pembayaran Masuk*

Pelanggan: {$user->nama_lengkap}  
Unit: {$car->brand->nama_merek} {$car->model}  
Jumlah: Rp" . number_format($p->total_bayar, 0, ',', '.') . "  

Order ID: {$p->payment_id}  
Status: Lunas"
    );
}


    // 🧾 Jika pembayaran charge (denda) sukses → update invoice jadi selesai
    if ($status === 'success' && $p->payment_type === 'charge' && $p->rental) {
        $invoice = \App\Models\Invoice::where('rental_id', $p->rental->rental_id)
            ->latest('invoice_id')
            ->first();

        if ($invoice && $invoice->status_invoice !== 'selesai') {
            $invoice->update(['status_invoice' => 'selesai']);
            Log::info('🧾 Invoice otomatis diupdate ke selesai dari callback', [
                'invoice_id' => $invoice->invoice_id,
                'rental_id'  => $p->rental->rental_id
            ]);
        }
    }
    return response()->json(['message' => 'Callback processed', 'status' => $status]);
}

    /* --------------------------------------------------------------------------
     | 🔁 Lanjutkan pembayaran
     * -------------------------------------------------------------------------- */
    public function continuePayment($id)
    {
        $p = Payment::with(['rental.car.brand', 'rental.user'])->findOrFail($id);
        abort_if($p->rental->user_id !== auth()->id(), 403);

        if ($p->expired_at && $p->expired_at->isPast())
            return $this->expirePayment($p, '⏰ Waktu pembayaran habis.');

        if ($p->status_pembayaran !== 'pending')
            return redirect()->route('user.payments.show', $p->payment_id)->with('info', 'Transaksi tidak aktif.');

        if (str_contains($p->payment_token, 'https://'))
            return redirect()->away($p->payment_token);

        $payload = $this->duitkuPayload($p->rental, $p, $p->metode);
        $res = Http::post('https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry', $payload)->json();

        if (!empty($res['paymentUrl'])) {
            $p->update(['gateway_reference' => $res['reference'], 'payment_token' => $res['paymentUrl'], 'expired_at' => now()->addMinutes(30)]);
            return redirect()->away($res['paymentUrl']);
        }

        return redirect()->route('user.payments.index')->with('error', 'Transaksi gagal diperbarui.');
    }

    private function expirePayment($p, $msg)
    {
        $p->update(['status_pembayaran' => 'failed', 'callback_status' => 'expired']);
        $p->rental?->update(['status_rental' => 'dibatalkan']);
        Invoice::where('rental_id', $p->rental_id)->first()?->update(['status_invoice' => 'dibatalkan']);
        return redirect()->route('user.payments.index')->with('error', $msg);
    }

    /* --------------------------------------------------------------------------
     | 📦 JSON untuk popup kuitansi
     * -------------------------------------------------------------------------- */
    public function json($id)
    {
        $p = Payment::with(['rental.car.brand', 'rental.invoice'])->findOrFail($id);
        $fmt = fn($v) => $v ? Carbon::parse($v)->format('d M Y, H:i') . ' WIB' : '-';
        $p->tanggal_bayar_fmt = $fmt($p->tanggal_bayar);
        $p->rental->tanggal_mulai_fmt = $fmt($p->rental->tanggal_mulai);
        $p->rental->tanggal_selesai_fmt = $fmt($p->rental->tanggal_selesai);
        return response()->json($p);
    }

    /* --------------------------------------------------------------------------
     | 📄 Download PDF kuitansi
     * -------------------------------------------------------------------------- */
    public function downloadReceipt($id)
    {
        $p = Payment::with(['rental.car.brand', 'rental.invoice'])->findOrFail($id);
        abort_if($p->rental->user_id !== auth()->id(), 403);

        try {
            $pdf = Pdf::loadView('user.payments.receipt_pdf', ['payment' => $p])->setPaper('a4', 'portrait');
            return response($pdf->output(), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="Kuitansi_'.$p->payment_id.'.pdf"');
        } catch (\Throwable $e) {
            Log::error('PDF error: ' . $e->getMessage());
            return response()->json(['error' => 'PDF gagal dibuat'], 500);
        }
    }

    /* --------------------------------------------------------------------------
     | 🧩 Helper: sinkron dari admin
     * -------------------------------------------------------------------------- */
    private function syncFromAdmin($p)
{
    if (!$p->rental) return;

    // 🚫 jangan cancel pembayaran tambahan (charge)
    if ($p->payment_type === 'charge') return;

    // 🚫 jangan sentuh payment yang sudah sukses
    if ($p->status_pembayaran !== 'pending') return;

    // cuma cancel kalau rental benar-benar dibatalkan oleh user/admin
    if ($p->rental->status_rental === 'dibatalkan') {
        $p->update([
            'status_pembayaran' => 'failed',
            'callback_status'   => 'cancelled',
        ]);

        Invoice::where('rental_id', $p->rental_id)->first()?->update([
            'status_invoice' => 'dibatalkan',
        ]);
    }
}

}
