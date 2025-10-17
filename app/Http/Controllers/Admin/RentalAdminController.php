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
    public function index()
{
    $rentals = Rental::with([
        'user',
        'car.brand',
        'payments' => function ($q) {
            // 🔹 Ambil hanya pembayaran utama (main)
            $q->where('payment_type', 'main');
        }
    ])
    ->where(function ($q) {
        // ✅ Tampilkan semua transaksi yang punya pembayaran utama
        $q->whereHas('payments', function ($p) {
            $p->where('payment_type', 'main');
        })
        // ✅ Atau tampilkan kalau ada pembayaran sukses (apapun jenisnya)
        ->orWhereHas('payments', function ($p) {
            $p->where('status_pembayaran', 'success');
        })
        // ✅ Atau tampilkan kalau rental sudah selesai / dibatalkan / berjalan
        ->orWhereIn('status_rental', ['selesai', 'dibatalkan', 'berjalan']);
    })
    ->orderByDesc('created_at')
    ->get();

    // 🧩 Setelah data diambil, pasangkan hanya payment utama ke variabel tunggal
    foreach ($rentals as $rental) {
        $rental->payment = $rental->payments->first();
    }

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