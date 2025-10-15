<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rental;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * ✅ STEP 1: User baru selesai isi form sewa → pilih metode pembayaran
     */
    public function detailRental($rental_id)
    {
        $rental = Rental::with(['car.brand', 'car.capacity', 'user'])->findOrFail($rental_id);
        if ($rental->user_id !== auth()->id()) abort(403);

        // kalau sudah ada pembayaran pending, arahkan langsung ke proses
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
     * ▶️ STEP 2: Kirim request ke Duitku & simpan payment pending
     */
    public function startProcess(Request $request, $rental_id)
    {
        $request->validate([
            'metode' => 'required|in:qris,bca,bri,bni,mandiri',
        ]);

        $rental = Rental::with(['car.brand', 'user'])->findOrFail($rental_id);
        if ($rental->user_id !== auth()->id()) abort(403);

        // Hindari double pending
        $existingPending = Payment::where('rental_id', $rental->rental_id)
            ->where('status_pembayaran', 'pending')
            ->first();
        if ($existingPending) {
            return redirect()->route('user.payments.process', $existingPending->payment_id);
        }

        // === DUITKU CONFIG ===
        $merchantCode = 'DS25394'; // kode sandbox kamu
        $apiKey = '06a924ce717ea70f6522e5c51241ccc6'; // sandbox API key kamu
        $merchantOrderId = 'INV-' . time();
        $amount = (int) $rental->total_biaya;
        $timestamp = round(microtime(true) * 1000); // dalam milidetik

        // mapping metode ke kode Duitku
        $mapMetode = [
            'qris' => 'QRIS',
            'bca' => 'BC',     // BCA VA
            'bri' => 'BR',     // BRI VA
            'bni' => 'N2',     // BNI VA
            'mandiri' => 'M2', // Mandiri VA
        ];

        $paymentMethod = $mapMetode[$request->metode] ?? 'QRIS';

        // ✅ Signature: merchantCode + merchantOrderId + paymentAmount + apiKey + timestamp
        $signature = md5($merchantCode . $merchantOrderId . $amount . $apiKey . $timestamp);

        // === Payload ke Duitku ===
        $payload = [
            "merchantCode" => $merchantCode,
            "paymentAmount" => $amount,
            "paymentMethod" => $paymentMethod,
            "merchantOrderId" => $merchantOrderId,
            "productDetails" => "Sewa Mobil " . ($rental->car->brand->nama_merek ?? ''),
            "email" => $rental->user->email,
            "phoneNumber" => $rental->user->no_hp ?? '08123456789',
            "customerVaName" => $rental->user->nama_lengkap ?? 'Penyewa',
            "callbackUrl" => "https://amiyah-mouselike-stably.ngrok-free.dev/api/payment/callback",
            "returnUrl" => route('user.payments.index'),
            "signature" => $signature,
            "timestamp" => $timestamp,
            "expiryPeriod" => 30
        ];

        // === Kirim ke Duitku API ===
        $response = Http::post('https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry', $payload);
        $result = $response->json();

        // 🧩 Cek hasil Duitku
        if (!isset($result['paymentUrl'])) {
            \Log::error('Duitku error response', ['result' => $result]);
            return back()->with('error', 'Gagal membuat transaksi: ' . ($result['Message'] ?? 'unknown'));
        }

        // === Simpan ke database ===
        DB::transaction(function () use ($rental, $result, $paymentMethod, $merchantOrderId, &$payment) {
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
        'expired_at'        => now()->addMinutes(30), // ⏱ tambah baris ini
    ]);

            $rental->update(['status_rental' => 'menunggu_pembayaran']);
        });

        // ✅ Redirect user ke halaman pembayaran Duitku
        return redirect()->away($result['paymentUrl']);
    }

    /**
     * 🧾 STEP 3: Halaman PROCESS — countdown & status
     */
    public function process($payment_id)
    {
        $payment = Payment::with('rental.car.brand')->findOrFail($payment_id);
        if ($payment->rental->user_id !== auth()->id()) abort(403);

        if ($payment->status_pembayaran !== 'pending') {
            return redirect()->route('user.payments.show', $payment->payment_id);
        }

        return view('user.payments.process', compact('payment'));
    }

    public function cancelSoft($payment_id)
    {
        $payment = Payment::with('rental')->findOrFail($payment_id);
        if ($payment->rental->user_id !== auth()->id()) abort(403);

        $payment->update(['status_pembayaran' => 'failed']);
        $payment->rental?->update(['status_rental' => 'dibatalkan']);

        return redirect()->route('user.payments.index')->with('success', 'Pembayaran dibatalkan.');
    }

    public function index()
    {
        $payments = Payment::with('rental.car.brand')
            ->whereHas('rental', fn($q) => $q->where('user_id', auth()->id()))
            ->orderByDesc('created_at')
            ->get();

        return view('user.payments.index', compact('payments'));
    }

    public function show($payment_id)
    {
        $payment = Payment::with('rental.car.brand')->findOrFail($payment_id);
        if ($payment->rental->user_id !== auth()->id()) abort(403);

        if ($payment->status_pembayaran === 'pending') {
            return redirect()->route('user.payments.process', $payment->payment_id);
        }

        return view('user.payments.success', compact('payment'));
    }

    public function checkExpired()
    {
        $expired = Payment::with('rental')
            ->where('status_pembayaran', 'pending')
            ->where('created_at', '<', Carbon::now()->subMinutes(30))
            ->get();

        foreach ($expired as $p) {
            $p->update(['status_pembayaran' => 'failed']);
            $p->rental?->update(['status_rental' => 'dibatalkan']);
        }

        return response()->json(['expired_updated' => $expired->count()]);
    }
}
