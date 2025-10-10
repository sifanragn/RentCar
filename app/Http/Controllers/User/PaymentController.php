<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rental;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * ✅ STEP 1: User baru selesai isi form sewa → masuk ke DETAIL (pilih metode).
     * - Belum membuat Payment apapun di sini. Belum dihitung transaksi.
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
     * ▶️ STEP 2: User klik “Bayar” di DETAIL → buat Payment (PENDING) dan arahkan ke PROCESS
     * - Di sinilah transaksi mulai dihitung (countdown 30 menit)
     */
    public function startProcess(Request $request, $rental_id)
    {
        $request->validate([
            'metode' => 'required|in:qris,bca,bri',
        ]);

        $rental = Rental::with('car.brand')->findOrFail($rental_id);
        if ($rental->user_id !== auth()->id()) abort(403);

        // Cegah dobel pending untuk rental yang sama
        $existingPending = Payment::where('rental_id', $rental->rental_id)
            ->where('status_pembayaran', 'pending')
            ->exists();

        if ($existingPending) {
            $payment = Payment::where('rental_id', $rental->rental_id)
                ->where('status_pembayaran', 'pending')
                ->latest()
                ->first();

            return redirect()->route('user.payments.process', $payment->payment_id);
        }

        DB::transaction(function () use ($rental, $request, &$payment) {
            $payment = Payment::create([
                'rental_id'         => $rental->rental_id,
                'gateway' => 'offline', // sementara
                'metode'            => $request->metode, // qris / bca / bri
                'total_bayar'       => $rental->total_biaya,
                'status_pembayaran' => 'pending',
                'gateway_reference' => 'MAN-' . mt_rand(100000, 999999),
                'payment_token'     => 'PAY-' . strtoupper(uniqid()),
                'callback_status'   => 'waiting',
                'tanggal_bayar'     => now(),
            ]);

            // kunci mobil sementara
            $rental->update(['status_rental' => 'menunggu_pembayaran']);
        });

        return redirect()->route('user.payments.process', $payment->payment_id);
    }

    /**
     * 🧾 STEP 3: Halaman PROCESS — tampilkan QR/rekening sesuai metode + countdown
     */
    public function process($payment_id)
    {
        $payment = Payment::with('rental.car.brand')->findOrFail($payment_id);
        if ($payment->rental->user_id !== auth()->id()) abort(403);

        // Kalau bukan pending, arahkan ke ringkasan (success/failed)
        if ($payment->status_pembayaran !== 'pending') {
            return redirect()->route('user.payments.show', $payment->payment_id);
        }

        return view('user.payments.process', compact('payment'));
    }

    /**
     * ❌ Tombol Batalkan di PROCESS — TIDAK mengubah status.
     * - Hanya kembali ke index (status tetap pending sampai 30 menit lewat → failed otomatis)
     */
    public function cancelSoft($payment_id)
{
    $payment = Payment::with('rental')->findOrFail($payment_id);

    // pastikan user adalah pemilik
    if ($payment->rental->user_id !== auth()->id()) {
        abort(403);
    }

    // ubah status payment & rental
    $payment->update(['status_pembayaran' => 'failed']);
    $payment->rental?->update(['status_rental' => 'dibatalkan']);

    return redirect()
        ->route('user.payments.index')
        ->with('success', 'Pembayaran berhasil dibatalkan.');
}

    /**
     * 📋 List semua pembayaran user (INDEX)
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
     * 🔍 Ringkasan setelah transaksi selesai (success/failed)
     * - Pending sebaiknya diarahkan ke PROCESS, bukan ke sini
     */
    public function show($payment_id)
    {
        $payment = Payment::with('rental.car.brand')->findOrFail($payment_id);
        if ($payment->rental->user_id !== auth()->id()) abort(403);

        if ($payment->status_pembayaran === 'pending') {
            // pending → lanjutkan ke process
            return redirect()->route('user.payments.process', $payment->payment_id);
        }

        return view('user.payments.success', compact('payment'));
    }


    /**
     * ⏱ Dipanggil berkala oleh JS untuk meng-expire payment pending > 30 menit
     */
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
