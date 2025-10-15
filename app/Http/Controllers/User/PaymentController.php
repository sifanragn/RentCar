<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rental;
use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class PaymentController extends Controller
{
    /**
     * ✅ STEP 1: Setelah user isi form sewa → pilih metode pembayaran.
     */
    public function detailRental($rental_id)
    {
        $rental = Rental::with(['car.brand', 'car.capacity', 'user'])->findOrFail($rental_id);
        abort_if($rental->user_id !== auth()->id(), 403);

        // Jika sudah ada payment pending untuk rental ini → lanjutkan
        $pending = Payment::where('rental_id', $rental->rental_id)
            ->where('status_pembayaran', 'pending')
            ->latest()
            ->first();

        if ($pending) {
            return redirect()->route('user.payments.process', $pending->payment_id);
        }

        return view('user.payments.detail', compact('rental'));
    }

    /**
     * ▶️ STEP 2: Klik “Bayar” → kirim ke Duitku dan redirect ke halaman pembayaran.
     */
    public function startProcess(Request $request, $rental_id)
{
    $request->validate([
        'metode' => 'required|in:qris,bca,bri',
    ]);

    // 🚫 Cek apakah user masih punya pembayaran utama (main) yang pending
    $hasPendingMain = Payment::whereHas('rental', function ($q) {
            $q->where('user_id', auth()->id());
        })
        ->where('payment_type', 'main')
        ->where('status_pembayaran', 'pending')
        ->where(function ($q) {
            $q->whereNull('expired_at')->orWhere('expired_at', '>', now());
        })
        ->exists();

    if ($hasPendingMain) {
        return redirect()->route('user.payments.index')
            ->with('warning', '⚠️ Kamu masih memiliki pembayaran utama yang belum diselesaikan. 
            Selesaikan atau batalkan pembayaran tersebut terlebih dahulu sebelum menyewa mobil lain.');
    }

    $rental = Rental::with(['car.brand', 'user'])->findOrFail($rental_id);
    abort_if($rental->user_id !== auth()->id(), 403);

    // Cegah duplikat pending pada rental yang sama
    if (Payment::where('rental_id', $rental->rental_id)
        ->where('status_pembayaran', 'pending')
        ->exists()) {
        $exist = Payment::where('rental_id', $rental->rental_id)
            ->where('status_pembayaran', 'pending')
            ->latest()
            ->first();
        return redirect()->route('user.payments.process', $exist->payment_id);
    }

    // 🟢 Buat payment lokal
    $payment = DB::transaction(function () use ($rental, $request) {
        $payment = Payment::create([
            'rental_id'         => $rental->rental_id,
            'gateway'           => 'Duitku',
            'metode'            => $request->metode,
            'payment_type'      => 'main',
            'total_bayar'       => $rental->total_biaya,
            'status_pembayaran' => 'pending',
            'gateway_reference' => 'MAN-' . mt_rand(100000, 999999),
            'payment_token'     => 'PAY-' . strtoupper(uniqid()),
            'callback_status'   => 'waiting',
            'tanggal_bayar'     => now(),
            'expired_at'        => now()->addMinutes(30),
        ]);

        $rental->update(['status_rental' => 'menunggu_pembayaran']);
        return $payment;
    });

    // 🔗 Buat transaksi ke Duitku
    $merchantCode    = 'DS25394';
    $apiKey          = '06a924ce717ea70f6522e5c51241ccc6';
    $amount          = (int) $rental->total_biaya;
    $merchantOrderId = 'ORDER-' . $payment->payment_id;
    $signature       = md5($merchantCode . $merchantOrderId . $amount . $apiKey);

    $mapMethod = [
        'qris' => 'QRIS',
        'bca'  => 'BC',
        'bri'  => 'BR',
    ];

    $payload = [
        "merchantCode"     => $merchantCode,
        "paymentAmount"    => $amount,
        "paymentMethod"    => $mapMethod[$request->metode],
        "merchantOrderId"  => $merchantOrderId,
        "productDetails"   => "Sewa Mobil " . ($rental->car->brand->nama_merek ?? ''),
        "email"            => $rental->user->email,
        "phoneNumber"      => $rental->user->no_hp ?? '08123456789',
        "customerVaName"   => $rental->user->nama_lengkap ?? 'Penyewa',
        "callbackUrl"      => "https://amiyah-mouselike-stably.ngrok-free.dev/api/payment/callback",
        "returnUrl"        => route('user.payments.index'),
        "signature"        => $signature,
        "expiryPeriod"     => 30,
    ];

    $response = Http::post('https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry', $payload);
    $result   = $response->json();

    if (!isset($result['paymentUrl'])) {
        return back()->with('error', 'Gagal membuat transaksi: ' . ($result['Message'] ?? 'unknown'));
    }

    $payment->update([
        'gateway_reference' => $result['reference'] ?? $payment->gateway_reference,
        'payment_token'     => $result['paymentUrl'],
    ]);

    return redirect()->away($result['paymentUrl']);
}


    /**
     * 🕐 Halaman status countdown lokal
     */
    public function process($payment_id)
{
    $payment = Payment::with(['rental.car.brand', 'rental.user'])->findOrFail($payment_id);
    abort_if($payment->rental->user_id !== auth()->id(), 403);

    // 🔄 Cek apakah admin sudah batalkan
    $this->syncFromAdmin($payment);

    // kalau sudah failed (dibatalkan oleh admin), arahkan ke halaman index
    if ($payment->status_pembayaran !== 'pending') {
        return redirect()->route('user.payments.index')
            ->with('error', '❌ Transaksi ini sudah dibatalkan oleh admin.');
    }

    return view('user.payments.process', compact('payment'));
}


    /**
     * ❌ Batalkan pembayaran manual oleh user
     */
    public function cancelSoft($payment_id)
    {
        $payment = Payment::findOrFail($payment_id);
        abort_if($payment->rental->user_id !== auth()->id(), 403);

        $payment->update([
            'status_pembayaran' => 'failed',
            'callback_status'   => 'cancelled',
        ]);

        $invoice = Invoice::where('rental_id', $payment->rental_id)->first();
        if ($invoice && strtolower($invoice->status_invoice) !== 'selesai') {
            $invoice->update(['status_invoice' => 'cancel']);
        }

        $payment->rental?->update(['status_rental' => 'dibatalkan']);

        return redirect()->route('user.payments.index')
            ->with('success', '❌ Pembayaran berhasil dibatalkan.');
    }

    /**
     * 📋 Daftar pembayaran user
     */
    public function index()
    {
        $payments = Payment::with('rental.car.brand')
            ->whereHas('rental', fn($q) => $q->where('user_id', auth()->id()))
            ->orderByDesc('created_at')
            ->get();

        return view('user.payments.index', compact('payments'));
    }

    /**
     * 🔍 Ringkasan pembayaran non-pending
     */
    public function show($payment_id)
    {
        $payment = Payment::with('rental.car.brand')->findOrFail($payment_id);
        abort_if($payment->rental->user_id !== auth()->id(), 403);

        if ($payment->status_pembayaran === 'pending') {
            return redirect()->route('user.payments.process', $payment->payment_id);
        }

        return view('user.payments.success', compact('payment'));
    }

    /**
     * ⏱ Auto expire — sinkronisasi expired dan invoice cancel
     */
    public function checkExpired()
    {
        $now = now();

        $cancelledRentals = Invoice::whereIn('status_invoice', ['cancel', 'dibatalkan'])
            ->pluck('rental_id');

        $expired = Payment::where('status_pembayaran', 'pending')
            ->where(function ($q) use ($now, $cancelledRentals) {
                $q->whereNotNull('expired_at')->where('expired_at', '<=', $now)
                  ->orWhereIn('rental_id', $cancelledRentals);
            })
            ->get();

        foreach ($expired as $payment) {
            $payment->update([
                'status_pembayaran' => 'failed',
                'callback_status'   => 'expired',
            ]);

            $payment->rental?->update(['status_rental' => 'dibatalkan']);

            $inv = Invoice::where('rental_id', $payment->rental_id)->first();
            if ($inv && strtolower($inv->status_invoice) !== 'selesai') {
                $inv->update(['status_invoice' => 'cancel']);
            }
        }

        return response()->json([
            'message' => 'Expired & cancelled synced.',
            'count'   => $expired->count(),
        ]);
    }

    /**
     * 🔁 Cek status real-time ke Duitku
     */
    public function checkStatus(Request $request, $payment_id)
    {
        $payment = Payment::findOrFail($payment_id);
        if (in_array($payment->status_pembayaran, ['success', 'failed'])) {
            return response()->json(['status' => $payment->status_pembayaran]);
        }

        $merchantCode    = 'DS25394';
        $apiKey          = '06a924ce717ea70f6522e5c51241ccc6';
        $merchantOrderId = 'ORDER-' . $payment->payment_id;
        $signature       = md5($merchantCode . $merchantOrderId . $apiKey);

        $payload = [
            'merchantCode'    => $merchantCode,
            'merchantOrderId' => $merchantOrderId,
            'signature'       => $signature,
        ];

        $response = Http::post('https://sandbox.duitku.com/webapi/api/merchant/transactionStatus', $payload);
        $result   = $response->json();

        $code = $result['statusCode'] ?? ($result['resultCode'] ?? null);

        if ($code === '00') {
            $payment->update(['status_pembayaran' => 'success', 'callback_status' => 'done']);
            $payment->rental?->update(['status_rental' => 'selesai']);
            Invoice::where('rental_id', $payment->rental_id)->first()?->update(['status_invoice' => 'selesai']);
            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'pending']);
    }

    /**
     * 🟢 Callback dari Duitku
     */
    public function callback(Request $request)
    {
        $merchantCode = 'DS25394';
        $apiKey       = '06a924ce717ea70f6522e5c51241ccc6';
        $signature    = md5($merchantCode . ($request->merchantOrderId ?? '') . ($request->amount ?? '') . $apiKey);

        if ($signature !== ($request->signature ?? '')) {
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        $payment = Payment::where('gateway_reference', $request->reference ?? '')
            ->orWhere('payment_token', $request->merchantOrderId ?? '')
            ->first();

        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        $resultCode = $request->resultCode ?? null;
        $newStatus = match ($resultCode) {
            '00' => 'success',
            '03' => 'failed',
            default => 'pending',
        };

        // Jangan ubah charge ke failed kalau belum confirm
        if ($payment->payment_type === 'charge' && $newStatus === 'failed') {
            return response()->json(['message' => 'Charge tetap pending.'], 200);
        }

        if ($payment->status_pembayaran !== $newStatus) {
            $payment->update([
                'status_pembayaran' => $newStatus,
                'callback_status'   => $newStatus === 'success' ? 'done' : ($newStatus === 'failed' ? 'error' : 'waiting'),
            ]);

            $payment->rental?->update([
                'status_rental' => $newStatus === 'success' ? 'selesai' : ($newStatus === 'failed' ? 'dibatalkan' : 'menunggu_pembayaran'),
            ]);

            Invoice::where('rental_id', $payment->rental_id)->first()?->update([
                'status_invoice' => $newStatus === 'success' ? 'selesai' : ($newStatus === 'failed' ? 'cancel' : 'pending'),
            ]);
        }

        return response()->json(['message' => 'Callback processed', 'status' => $newStatus]);
    }

    /**
 * 🔄 Sinkronisasi status dari admin (jika admin sudah batalkan di panel)
 */
private function syncFromAdmin($payment)
{
    // kalau admin sudah batalkan rental tapi user masih pending
    if ($payment->rental && $payment->rental->status_rental === 'dibatalkan' 
        && $payment->status_pembayaran === 'pending') {

        $payment->update([
            'status_pembayaran' => 'failed',
            'callback_status'   => 'cancelled',
        ]);

        $inv = \App\Models\Invoice::where('rental_id', $payment->rental_id)->first();
        if ($inv && strtolower($inv->status_invoice) !== 'selesai') {
            $inv->update(['status_invoice' => 'cancel']);
        }
    }
}


    /**
     * 🟢 Return URL dari Duitku (user kembali ke app)
     */
    public function successView(Request $request)
    {
        $orderId     = $request->query('order') ?? $request->query('merchantOrderId');
        $resultCode  = $request->query('resultCode');
        $reference   = $request->query('reference');

        $payment = Payment::where('gateway_reference', $reference)
            ->orWhere('payment_id', str_replace('ORDER-', '', (string) $orderId))
            ->first();

        if (!$payment) {
            return redirect()->route('user.payments.index')
                ->with('error', 'Data pembayaran tidak ditemukan.');
        }

        if ($resultCode === '00') {
            $payment->update(['status_pembayaran' => 'success', 'callback_status' => 'done']);
            $payment->rental?->update(['status_rental' => 'selesai']);
            Invoice::where('rental_id', $payment->rental_id)->first()?->update(['status_invoice' => 'selesai']);
            return view('user.payments.success', compact('payment'));
        }

        if (in_array($resultCode, ['01', '02']) || $resultCode === null) {
            $payment->update(['status_pembayaran' => 'pending', 'callback_status' => 'waiting']);
            $payment->rental?->update(['status_rental' => 'menunggu_pembayaran']);
            Invoice::where('rental_id', $payment->rental_id)->first()?->update(['status_invoice' => 'pending']);
            return redirect()->route('user.payments.index')
                ->with('info', 'Pembayaran masih menunggu. Kamu bisa lanjutkan kapan saja.');
        }

        if ($resultCode === '03') {
            $payment->update(['status_pembayaran' => 'failed', 'callback_status' => 'error']);
            $payment->rental?->update(['status_rental' => 'dibatalkan']);
            Invoice::where('rental_id', $payment->rental_id)->first()?->update(['status_invoice' => 'cancel']);
            return redirect()->route('user.payments.index')
                ->with('error', 'Pembayaran dibatalkan.');
        }

        return redirect()->route('user.payments.index')
            ->with('info', 'Menunggu konfirmasi dari gateway.');
    }

    /**
     * 🔁 Lanjutkan pembayaran ke Duitku
     */
   public function continuePayment($payment_id)
{
    $payment = Payment::with(['rental.car.brand', 'rental.user'])->findOrFail($payment_id);
    abort_if($payment->rental->user_id !== auth()->id(), 403);

    // ⏰ 1️⃣ Cek apakah waktu pembayaran sudah lewat (expired)
    if ($payment->expired_at && $payment->expired_at->isPast()) {
        $payment->update([
            'status_pembayaran' => 'failed',
            'callback_status'   => 'expired',
        ]);

        $payment->rental?->update(['status_rental' => 'dibatalkan']);

        $invoice = \App\Models\Invoice::where('rental_id', $payment->rental_id)->first();
        if ($invoice && strtolower($invoice->status_invoice) !== 'selesai') {
            $invoice->update(['status_invoice' => 'cancel']);
        }

        return redirect()->route('user.payments.index')
            ->with('error', '⏰ Waktu pembayaran telah habis. Transaksi otomatis dibatalkan.');
    }

    // 🚫 2️⃣ Kalau status sudah bukan pending (sudah dibayar / gagal)
    if ($payment->status_pembayaran !== 'pending') {
        return redirect()->route('user.payments.show', $payment->payment_id)
            ->with('info', 'Transaksi ini sudah tidak aktif.');
    }

    // 🔗 3️⃣ Kalau sudah ada link Duitku valid → langsung redirect
    if (!empty($payment->payment_token) && str_contains($payment->payment_token, 'https://')) {
        return redirect()->away($payment->payment_token);
    }

    // 🟡 4️⃣ Kalau masih dummy token (WAIT-xxxx / PAY-xxxx)
    if (str_starts_with($payment->payment_token, 'WAIT-') || str_starts_with($payment->payment_token, 'PAY-')) {

        $merchantCode = 'DS25394';
        $apiKey       = '06a924ce717ea70f6522e5c51241ccc6';

        // Bedakan antara main & charge (invoice)
        $merchantOrderId = $payment->payment_type === 'charge'
            ? 'INV' . ($payment->rental->invoice->invoice_id ?? $payment->rental_id) . '-P' . $payment->id
            : 'ORDER-' . $payment->payment_id;

        $amount = (int) $payment->total_bayar;
        $signature = md5($merchantCode . $merchantOrderId . $amount . $apiKey);

        $mapMethod = [
            'qris'     => 'QRIS',
            'bca'      => 'BC',
            'bri'      => 'BR',
            'bni'      => 'N2',
            'mandiri'  => 'M2',
        ];
        $method = $mapMethod[$payment->metode] ?? 'QRIS';

        // 📡 Kirim request baru ke Duitku
        $payload = [
            "merchantCode"     => $merchantCode,
            "paymentAmount"    => $amount,
            "paymentMethod"    => $method,
            "merchantOrderId"  => $merchantOrderId,
            "productDetails"   => "Pembayaran " . strtoupper($payment->payment_type) . " sewa mobil " . ($payment->rental->car->brand->nama_merek ?? ''),
            "email"            => $payment->rental->user->email,
            "phoneNumber"      => $payment->rental->user->no_hp ?? '08123456789',
            "customerVaName"   => $payment->rental->user->nama_lengkap ?? 'Penyewa',
            "callbackUrl"      => "https://amiyah-mouselike-stably.ngrok-free.dev/api/payment/callback",
            "returnUrl"        => route('user.payments.index'),
            "signature"        => $signature,
            "expiryPeriod"     => 30,
        ];

        $response = Http::post('https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry', $payload);
        $result   = $response->json();

        \Log::info('🔁 Duitku Re-inquiry from continuePayment()', [
            'payload' => $payload,
            'result'  => $result,
        ]);

        // ✅ 5️⃣ Jika berhasil dapat payment URL baru
        if (!empty($result['paymentUrl'])) {
            $payment->update([
                'gateway_reference' => $result['reference'] ?? $payment->gateway_reference,
                'payment_token'     => $result['paymentUrl'],
                'expired_at'        => now()->addMinutes(30),
            ]);

            return redirect()->away($result['paymentUrl']);
        }

        // ⚠️ 6️⃣ Kalau Duitku respon pending
        if (($result['statusCode'] ?? $result['resultCode'] ?? null) === '01') {
            return redirect()->route('user.payments.process', $payment->payment_id)
                ->with('info', 'Link pembayaran sedang diproses, coba lagi beberapa detik.');
        }

        // ✅ 7️⃣ Kalau transaksi sudah sukses di Duitku
        if (($result['statusCode'] ?? $result['resultCode'] ?? null) === '00') {
            $payment->update(['status_pembayaran' => 'success', 'callback_status' => 'done']);
            $payment->rental?->update(['status_rental' => 'selesai']);
            \App\Models\Invoice::where('rental_id', $payment->rental_id)->first()?->update(['status_invoice' => 'selesai']);

            return redirect()->route('user.payments.show', $payment->payment_id)
                ->with('success', '✅ Pembayaran sudah berhasil.');
        }

        // ❌ 8️⃣ Kalau gagal / tidak aktif
        $payment->update(['status_pembayaran' => 'failed', 'callback_status' => 'expired']);
        $payment->rental?->update(['status_rental' => 'dibatalkan']);
        \App\Models\Invoice::where('rental_id', $payment->rental_id)->first()?->update(['status_invoice' => 'cancel']);

        return redirect()->route('user.payments.index')
            ->with('error', 'Transaksi sudah tidak aktif atau gagal. Silakan buat ulang pembayaran.');
    }

    // 🔚 9️⃣ Fallback terakhir
    return redirect()->route('user.payments.index')
        ->with('error', 'Link pembayaran tidak ditemukan atau belum tersedia.');
}

      /**
 * 🔍 Basic JSON detail pembayaran (umum)
 */
public function showJson($id)
{
    $payment = \App\Models\Payment::with([
        'rental.car.brand',
        'rental.user',
        'rental.invoice', // ✅ tambahkan relasi invoice
    ])->findOrFail($id);

    return response()->json($payment);
}

/**
 * 📦 API JSON untuk popup kuitansi (sinkron tanggal & jam)
 */
public function json($id)
{
    $payment = \App\Models\Payment::with([
        'rental.car.brand',
        'rental.invoice', // ✅ tambahkan relasi invoice
    ])->findOrFail($id);

    // Helper format tanggal ke WIB
    $fmt = fn($val) => $val
        ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $val, 'Asia/Jakarta')
            ->format('d M Y, H:i') . ' WIB'
        : '-';

    // Tambahkan field tambahan yang diformat
    $payment->tanggal_bayar_fmt = $fmt($payment->tanggal_bayar);
    $payment->rental->tanggal_mulai_fmt = $fmt($payment->rental->tanggal_mulai);
    $payment->rental->tanggal_selesai_fmt = $fmt($payment->rental->tanggal_selesai);

    return response()->json($payment);
}


}