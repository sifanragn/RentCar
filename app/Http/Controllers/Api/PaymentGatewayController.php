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
     * STEP 1:
     * Menampilkan detail rental setelah user mengisi form sewa
     * dan mengarahkan ke proses pembayaran jika sudah ada transaksi pending
     */
    public function detailRental($rental_id)
    {
        $rental = Rental::with(['car.brand', 'car.capacity', 'user'])->findOrFail($rental_id);

        // Pastikan hanya pemilik rental yang bisa akses
        if ($rental->user_id !== auth()->id()) abort(403);

        // Cek apakah ada pembayaran yang masih pending
        $pending = Payment::where('rental_id', $rental->rental_id)
            ->where('status_pembayaran', 'pending')
            ->latest()
            ->first();

        // Jika ada, langsung arahkan ke halaman proses
        if ($pending) {
            return redirect()->route('user.payments.process', $pending->payment_id);
        }

        return view('user.payments.detail', compact('rental'));
    }

    /**
     * STEP 2:
     * Memulai proses pembayaran → kirim data ke Duitku
     * dan menyimpan transaksi ke database
     */
    public function startProcess(Request $request, $rental_id)
    {
        // ================= VALIDASI INPUT =================
        $request->validate([
            'metode' => 'required|in:qris,bca,bri,bni,mandiri',
        ]);

        $rental = Rental::with(['car.brand', 'user'])->findOrFail($rental_id);

        // Validasi kepemilikan rental
        if ($rental->user_id !== auth()->id()) abort(403);

        // ================= CEGAH DOUBLE TRANSAKSI =================
        $existingPending = Payment::where('rental_id', $rental->rental_id)
            ->where('status_pembayaran', 'pending')
            ->first();

        if ($existingPending) {
            return redirect()->route('user.payments.process', $existingPending->payment_id);
        }

        // ================= SETUP PAYMENT GATEWAY (DUITKU) =================
        $merchantCode = 'DS25394'; // kode merchant (sandbox)
        $apiKey = '06a924ce717ea70f6522e5c51241ccc6'; // API key
        $merchantOrderId = 'INV-' . time(); // ID unik transaksi
        $amount = $rental->total_biaya;

        // Mapping metode pembayaran ke kode Duitku
        $mapMetode = [
            'qris' => 'QRIS',
            'bca' => 'VC',
            'bri' => 'B1',
            'bni' => 'I1',
            'mandiri' => 'M1',
        ];

        $paymentMethod = $mapMetode[$request->metode];

        // Generate signature untuk keamanan
        $signature = md5($merchantCode . $merchantOrderId . $amount . $apiKey);

        // ================= DATA YANG DIKIRIM KE DUITKU =================
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
            "expiryPeriod" => 30 // masa berlaku pembayaran (menit)
        ];

        // ================= REQUEST KE DUITKU =================
        $response = Http::post(
            'https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry',
            $payload
        );

        $result = $response->json();

        // Jika gagal
        if (!isset($result['paymentUrl'])) {
            return back()->with('error', 'Gagal membuat transaksi: ' . ($result['Message'] ?? 'unknown'));
        }

        // ================= SIMPAN TRANSAKSI KE DATABASE =================
        DB::transaction(function () use ($rental, $request, $result, $merchantOrderId, &$payment) {

            $payment = Payment::create([
                'rental_id'         => $rental->rental_id,
                'gateway'           => 'Duitku',
                'metode'            => $request->metode,
                'payment_type'      => 'main',
                'total_bayar'       => $rental->total_biaya,
                'status_pembayaran' => 'pending',
                'gateway_reference' => $result['reference'] ?? ('MAN-' . rand(100000, 999999)),
                'payment_token'     => $result['paymentUrl'],
                'merchant_order_id' => $merchantOrderId,
                'callback_status'   => 'waiting',
                'tanggal_bayar'     => now(),
                'expired_at'        => now()->addMinutes(30),
            ]);

            // Update status rental
            $rental->update(['status_rental' => 'menunggu_pembayaran']);
        });

        // Redirect ke halaman pembayaran Duitku
        return redirect($result['paymentUrl']);
    }

    /**
     * STEP 3:
     * Menampilkan halaman proses pembayaran (status & countdown)
     */
    public function process($payment_id)
    {
        $payment = Payment::with('rental.car.brand')->findOrFail($payment_id);

        // Validasi user
        if ($payment->rental->user_id !== auth()->id()) abort(403);

        // Jika sudah tidak pending, arahkan ke hasil
        if ($payment->status_pembayaran !== 'pending') {
            return redirect()->route('user.payments.show', $payment->payment_id);
        }

        return view('user.payments.process', compact('payment'));
    }

    // Membatalkan pembayaran secara manual (soft cancel)
    public function cancelSoft($payment_id)
    {
        $payment = Payment::with('rental')->findOrFail($payment_id);

        if ($payment->rental->user_id !== auth()->id()) abort(403);

        // Ubah status pembayaran & rental
        $payment->update(['status_pembayaran' => 'failed']);
        $payment->rental?->update(['status_rental' => 'dibatalkan']);

        return redirect()->route('user.payments.index')
            ->with('success', 'Pembayaran dibatalkan.');
    }

    // Menampilkan riwayat pembayaran user
    public function index()
    {
        $payments = Payment::with('rental.car.brand')
            ->whereHas('rental', fn($q) => $q->where('user_id', auth()->id()))
            ->orderByDesc('created_at')
            ->get();

        return view('user.payments.index', compact('payments'));
    }

    // Menampilkan detail pembayaran
    public function show($payment_id)
    {
        $payment = Payment::with('rental.car.brand')->findOrFail($payment_id);

        if ($payment->rental->user_id !== auth()->id()) abort(403);

        // Jika masih pending, kembali ke proses
        if ($payment->status_pembayaran === 'pending') {
            return redirect()->route('user.payments.process', $payment->payment_id);
        }

        return view('user.payments.success', compact('payment'));
    }

    // Mengecek dan mengupdate pembayaran yang sudah expired
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

        return response()->json([
            'expired_updated' => $expired->count()
        ]);
    }
}