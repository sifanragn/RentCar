<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use Illuminate\Http\Request;

class RentalAdminController extends Controller
{
    /**
     * 📋 Daftar semua penyewaan
     */
   public function index(Request $request)
{
    $query = Rental::with(['user', 'car.brand']);

    // 🔍 Filter berdasarkan nama penyewa
    if ($request->filled('nama_penyewa')) {
        $query->whereHas('user', function ($q) use ($request) {
            $q->where('nama_lengkap', 'like', '%' . $request->nama_penyewa . '%');
        });
    }

    // 📅 Filter tanggal sewa
    if ($request->filled('tanggal_mulai')) {
        $query->whereDate('tanggal_mulai', '>=', $request->tanggal_mulai);
    }

    if ($request->filled('tanggal_selesai')) {
        $query->whereDate('tanggal_selesai', '<=', $request->tanggal_selesai);
    }

    // 🟢 Filter status
    if ($request->filled('status_rental')) {
        $query->where('status_rental', $request->status_rental);
    }

    $rentals = $query->orderByDesc('created_at')->get();

    return view('admin.rentals.index', compact('rentals'));
}

    /**
     * 📄 Detail transaksi
     */
   public function show($id)
{
    $rental = Rental::with([
        'user',
        'car.brand',
        'payments' => function($q) {
            // ambil hanya pembayaran utama
            $q->where('payment_type', 'main');
        }
    ])->findOrFail($id);

    // pastikan hanya ambil pembayaran utama
    $rental->payment = $rental->payments->first();

    return view('admin.rentals.show', compact('rental'));
}


    /**
     * ⚙️ Ubah status rental (verifikasi → menunggu / berjalan / selesai / dibatalkan)
     */
    public function updateStatus(Request $request, $rental_id)
    {
        $request->validate([
            'status_rental' => 'required|in:berjalan,selesai,dibatalkan',
        ]);

        $rental = Rental::with('payment')->findOrFail($rental_id);

        // update status di tabel rentals
        $rental->update(['status_rental' => $request->status_rental]);

        // 🔁 sinkronisasi otomatis ke tabel payments
      if ($rental->mainPayment) {
    if ($request->status_rental === 'selesai') {
        $rental->mainPayment->update(['status_pembayaran' => 'success']);
    } elseif ($request->status_rental === 'dibatalkan') {
        $rental->mainPayment->update(['status_pembayaran' => 'failed']);
    }
        return redirect()->route('admin.rentals.show', $rental->rental_id)
            ->with('success', 'Status penyewaan diperbarui.');
    }
    }


}