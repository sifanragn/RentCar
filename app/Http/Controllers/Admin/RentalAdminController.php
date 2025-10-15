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
        $rentals = Rental::with(['user', 'car.brand', 'payment'])
            ->where(function ($q) {
                // ✅ Tampilkan semua transaksi utama (main) walau pending
                $q->whereHas('payment', function ($p) {
                    $p->where('payment_type', 'main');
                })

                // ✅ Atau tampilkan kalau payment-nya sudah sukses (main/charge)
                ->orWhereHas('payment', function ($p) {
                    $p->where('status_pembayaran', 'success');
                })

                // ✅ Atau tampilkan kalau rental-nya sudah selesai / dibatalkan
                ->orWhereIn('status_rental', ['selesai', 'dibatalkan', 'berjalan']);
            })
            ->orderByDesc('created_at')
            ->get();

        return view('admin.rentals.index', compact('rentals'));
    }

    /**
     * 📄 Detail transaksi
     */
    public function show($id)
    {
        $rental = Rental::with(['user', 'car.brand', 'payment'])->findOrFail($id);
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
        if ($rental->payment) {
            if ($request->status_rental === 'selesai') {
                $rental->payment->update(['status_pembayaran' => 'success']);
            } elseif ($request->status_rental === 'dibatalkan') {
                $rental->payment->update(['status_pembayaran' => 'failed']);
            }
        }

        // ✅ redirect otomatis
        if (in_array($request->status_rental, ['selesai', 'dibatalkan'])) {
            return redirect()->route('admin.rentals.index')
                ->with('success', 'Status penyewaan & pembayaran berhasil diperbarui.');
        }

        return redirect()->route('admin.rentals.show', $rental->rental_id)
            ->with('success', 'Status penyewaan diperbarui.');
    }
}
