<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rental;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class PaymentGatewayController extends Controller
{
    /**
     * ✅ STEP 1: User baru selesai isi form sewa → masuk ke DETAIL (pilih metode).
     */
    public function detailRental($rental_id)
    {
        $rental = Rental::with(['car.brand', 'car.capacity', 'user'])->findOrFail($rental_id);
        if ($rental->user_id !== auth()->id()) abort(403);

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
     * ▶️ STEP 2: User klik “Bayar” di DETAIL → kirim ke Duitku & buat Payment.
     */
    public function startProcess(Request $request, $rental_id)
    {
        $request->validate([
            'metode' => 'required|in:qris,bca,bri,bni,mandiri',
        ]);

        $rental = Rental::with(['car.brand', 'user'])->findOrFail($rental_id);
        if ($rental->user_id !== auth()->id()) abort(403);

        // Cegah double pending
        $existingPending = Payment::where('rental_id', $rental->rental_id)
            ->where('status_pembayaran', 'pending')
            ->first();

        if ($existingPending) {
            return redirect()->route('user.payments.process', $existingPending->payment_id);
        }

        // === Setup Duitku ===
        $merchantCode = 'DS25394'; // contoh sandbox
        $apiKey = ''; // ubah sesuai key sandbox kamu
        $merchantOrderId = 'INV-' . time();
        $amount = $rental->total_biaya;

        $mapMetode = [
            'qris' => 'QRIS',
            'bca' => 'VC',
            'bri' => 'B1',
            'bni' => 'I1',
            'mandiri' => 'M1',
        ];
        $paymentMethod = $mapMetode[$request->metode];
        $signature = md5($merchantCode . $merchantOrderId . $amount . $apiKey);

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
            "expiryPeriod" => 30
        ];

        $response = Http::post('https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry', $payload);
        $result = $response->json();

        if (!isset($result['paymentUrl'])) {
            return back()->with('error', 'Gagal membuat transaksi: ' . ($result['Message'] ?? 'unknown'));
        }

        // Simpan ke DB
        DB::transaction(function () use ($rental, $request, $result, $paymentMethod, $merchantOrderId, &$payment) {
            $payment = Payment::create([
                'rental_id' => $rental->rental_id,
                'gateway' => 'Duitku',
                'metode' => $paymentMethod,
                'total_bayar' => $rental->total_biaya,
                'status_pembayaran' => 'pending',
                'gateway_reference' => $merchantOrderId,
                'payment_token' => $result['reference'] ?? null,
                'callback_status' => 'waiting',
                'tanggal_bayar' => now(),
            ]);

            $rental->update(['status_rental' => 'menunggu_pembayaran']);
        });

        // Redirect ke halaman Duitku
        return redirect($result['paymentUrl']);
    }

    /**
     * 🧾 STEP 3: Halaman PROCESS — countdown & status.
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
