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
    private string $merchantCode = 'DS25394';
    private string $apiKey = '06a924ce717ea70f6522e5c51241ccc6';
    private string $callbackUrl = 'https://amiyah-mouselike-stably.ngrok-free.dev/api/payment/callback';

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

        // 🚫 Cegah duplikat pending
        if (Payment::where('rental_id', $rental->rental_id)->where('status_pembayaran', 'pending')->exists()) {
            $exist = Payment::where('rental_id', $rental->rental_id)->latest()->first();
            return redirect()->route('user.payments.process', $exist->payment_id);
        }

        // 🟢 Simpan ke DB
        $payment = DB::transaction(function () use ($rental, $r) {
            $p = Payment::create([
                'rental_id' => $rental->rental_id,
                'gateway' => 'Duitku',
                'metode' => $r->metode,
                'payment_type' => 'main',
                'total_bayar' => $rental->total_biaya,
                'status_pembayaran' => 'pending',
                'gateway_reference' => 'MAN-' . rand(100000, 999999),
                'payment_token' => 'WAIT-' . strtoupper(uniqid()),
                'callback_status' => 'waiting',
                'tanggal_bayar' => now(),
                'expired_at' => now()->addMinutes(30),
            ]);
            $rental->update(['status_rental' => 'menunggu_pembayaran']);
            return $p;
        });

        // 🔗 Request ke Duitku
        $payload = $this->duitkuPayload($rental, $payment, $r->metode);
        $res = Http::post('https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry', $payload)->json();

        if (empty($res['paymentUrl'])) return back()->with('error', 'Gagal membuat transaksi.');

        $payment->update([
            'gateway_reference' => $res['reference'] ?? $payment->gateway_reference,
            'payment_token' => $res['paymentUrl'],
        ]);

        return redirect()->away($res['paymentUrl']);
    }

    private function duitkuPayload($rental, $payment, $metode)
    {
        $map = ['qris' => 'QRIS', 'bca' => 'BC', 'bri' => 'BR'];
        $amount = (int)$rental->total_biaya;
        $orderId = 'ORDER-' . $payment->payment_id;
        return [
            "merchantCode" => $this->merchantCode,
            "paymentAmount" => $amount,
            "paymentMethod" => $map[$metode] ?? 'QRIS',
            "merchantOrderId" => $orderId,
            "productDetails" => "Sewa Mobil " . ($rental->car->brand->nama_merek ?? ''),
            "email" => $rental->user->email,
            "phoneNumber" => $rental->user->no_hp ?? '08123456789',
            "customerVaName" => $rental->user->nama_lengkap,
            "callbackUrl" => $this->callbackUrl,
            "returnUrl" => route('user.payments.index'),
            "signature" => md5($this->merchantCode . $orderId . $amount . $this->apiKey),
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
        Invoice::where('rental_id', $p->rental_id)->first()?->update(['status_invoice' => 'cancel']);

        return back()->with('success', '❌ Pembayaran dibatalkan.');
    }

    /* --------------------------------------------------------------------------
     | 📋 Daftar pembayaran (index)
     * -------------------------------------------------------------------------- */
    public function index(Request $r)
    {
        $q = Payment::with(['rental.car.brand', 'rental.invoice'])
            ->whereHas('rental', fn($q) => $q->where('user_id', auth()->id()));

        if ($r->filled('no_transaksi'))
            $q->whereHas('rental.invoice', fn($i) => $i->where('invoice_id', str_replace('INV', '', $r->no_transaksi)));
        if ($r->filled('tanggal'))
            $q->whereDate('created_at', $r->tanggal);
        if ($r->filled('status'))
            $q->where('status_pembayaran', $r->status);

        return view('user.payments.index', ['payments' => $q->latest()->get()]);
    }

    /* --------------------------------------------------------------------------
     | ⏱️ Auto expire + sinkron cancel
     * -------------------------------------------------------------------------- */
    public function checkExpired()
    {
        $now = now();
        $cancelled = Invoice::whereIn('status_invoice', ['cancel', 'dibatalkan'])->pluck('rental_id');
        $expired = Payment::where('status_pembayaran', 'pending')
            ->where(fn($q) => $q->where('expired_at', '<=', $now)->orWhereIn('rental_id', $cancelled))
            ->get();

        foreach ($expired as $p) {
            $p->update(['status_pembayaran' => 'failed', 'callback_status' => 'expired']);
            $p->rental?->update(['status_rental' => 'dibatalkan']);
            Invoice::where('rental_id', $p->rental_id)->first()?->update(['status_invoice' => 'cancel']);
        }

        return response()->json(['message' => 'Synced', 'count' => $expired->count()]);
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
        $sign = md5($this->merchantCode . ($r->merchantOrderId ?? '') . ($r->amount ?? '') . $this->apiKey);
        if ($sign !== ($r->signature ?? '')) return response()->json(['message' => 'Invalid signature'], 400);

        $p = Payment::where('gateway_reference', $r->reference)->first();
        if (!$p) return response()->json(['message' => 'Payment not found'], 404);

        $status = match ($r->resultCode ?? '') {
            '00' => 'success', '03' => 'failed', default => 'pending',
        };

        if ($p->payment_type === 'charge' && $status === 'failed')
            return response()->json(['message' => 'Charge tetap pending.']);

        $p->update([
            'status_pembayaran' => $status,
            'callback_status' => $status === 'success' ? 'done' : ($status === 'failed' ? 'error' : 'waiting'),
        ]);

        $p->rental?->update(['status_rental' => $status === 'success' ? 'selesai' : ($status === 'failed' ? 'dibatalkan' : 'menunggu_pembayaran')]);
        Invoice::where('rental_id', $p->rental_id)->first()?->update(['status_invoice' => $status === 'success' ? 'selesai' : ($status === 'failed' ? 'cancel' : 'pending')]);

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
        Invoice::where('rental_id', $p->rental_id)->first()?->update(['status_invoice' => 'cancel']);
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
        if ($p->rental && $p->rental->status_rental === 'dibatalkan' && $p->status_pembayaran === 'pending') {
            $p->update(['status_pembayaran' => 'failed', 'callback_status' => 'cancelled']);
            Invoice::where('rental_id', $p->rental_id)->first()?->update(['status_invoice' => 'cancel']);
        }
    }
}
