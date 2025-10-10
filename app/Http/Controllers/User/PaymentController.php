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
     * 💳 Membuat pembayaran baru
     */
    public function store(Request $request, $rental_id)
    {
        $rental = Rental::findOrFail($rental_id);
        if ($rental->user_id !== auth()->id()) abort(403);

        // Cegah duplikat payment
        $existing = Payment::where('rental_id', $rental_id)->latest()->first();
        if ($existing) {
            return redirect()->route('user.payments.show', $existing->payment_id);
        }

        DB::transaction(function () use ($rental, &$payment) {
            $payment = Payment::create([
                'rental_id'         => $rental->rental_id,
                'gateway'           => 'Manual',
                'metode'            => 'qris',
                'total_bayar'       => $rental->total_biaya,
                'status_pembayaran' => 'pending',
                'gateway_reference' => 'MAN-' . mt_rand(100000, 999999),
                'payment_token'     => 'PAY-' . strtoupper(uniqid()),
                'callback_status'   => 'waiting',
                'tanggal_bayar'     => now(),
            ]);
            $rental->update(['status_rental' => 'menunggu_pembayaran']);
        });

        return redirect()->route('user.payments.show', $payment->payment_id);
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
     * 🔍 Menampilkan detail / pending
     */
    public function show($payment_id)
    {
        $payment = Payment::with('rental.car.brand')->findOrFail($payment_id);
        if ($payment->rental->user_id !== auth()->id()) abort(403);

        if ($payment->status_pembayaran === 'pending') {
            return view('user.payments.pending', compact('payment'));
        }

        return view('user.payments.detail', compact('payment'));
    }

    /**
     * 🧾 Halaman proses (QRIS + countdown)
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

    /**
     * ❌ Batalkan manual
     */
    public function cancel($payment_id)
    {
        $payment = Payment::with('rental')->findOrFail($payment_id);
        if ($payment->rental->user_id !== auth()->id()) abort(403);

        if ($payment->status_pembayaran === 'pending') {
            DB::transaction(function () use ($payment) {
                $payment->update(['status_pembayaran' => 'failed']);
                $payment->rental->update(['status_rental' => 'dibatalkan']);
            });
        }

        return redirect()->route('user.payments.index');
    }

    /**
     * ⏱ Auto-expire payment yang lewat 30 menit
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
