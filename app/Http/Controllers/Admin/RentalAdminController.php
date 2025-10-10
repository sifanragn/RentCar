<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Models\Car;
use Illuminate\Http\Request;

class RentalAdminController extends Controller
{
    /**
     * 📋 Daftar semua penyewaan
     */
    public function index()
    {
        $rentals = Rental::with(['user', 'car.brand'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.rentals.index', compact('rentals'));
    }

    /**
     * 📄 Detail transaksi
     */
    public function show($id)
    {
        $rental = Rental::with(['user', 'car.brand'])->findOrFail($id);
        return view('admin.rentals.show', compact('rental'));
    }

    /**
     * ⚙️ Ubah status rental (verifikasi → menunggu / berjalan / selesai / dibatalkan)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_rental' => 'required|in:verifikasi_diperlukan,menunggu,berjalan,selesai,dibatalkan',
        ]);

        $rental = Rental::findOrFail($id);
        $rental->update(['status_rental' => $request->status_rental]);

        // Jika selesai / dibatalkan → mobil jadi tersedia lagi
        if (in_array($request->status_rental, ['selesai', 'dibatalkan'])) {
            Car::where('car_id', $rental->car_id)->update(['status' => 'tersedia']);
        }

        return back()->with('success', 'Status rental berhasil diperbarui.');
    }
}
