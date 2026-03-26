<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CarBrand;
use App\Models\CarCapacity;
use Illuminate\Http\Request;
use App\Models\Rental;

class CarController extends Controller
{
    /**
     * 🏎️ Tampilkan daftar mobil untuk user dengan filter & rekomendasi
     */
    public function index(Request $request)
    {
        // Query dasar: hanya mobil tersedia
        $query = Car::with(['brand', 'capacity'])
            ->where('status', 'tersedia');

        // 🔹 Filter berdasarkan merek
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        // 🔹 Filter berdasarkan kapasitas
        if ($request->filled('capacity_id')) {
            $query->where('capacity_id', $request->capacity_id);
        }

        // 🔹 Pencarian berdasarkan model mobil
        if ($request->filled('search')) {
            $query->where('model', 'like', '%' . $request->search . '%');
        }

        // Ambil hasil query
        $cars = $query->orderBy('created_at', 'desc')->get();

        // 🔹 Jika hasil kosong → tampilkan saran mobil lain
        $suggestions = collect();
        if ($cars->isEmpty()) {
            $suggestions = Car::with(['brand'])
                ->where('status', 'tersedia')
                ->inRandomOrder()
                ->take(3)
                ->get();
        }

        // 🔹 Data tambahan untuk filter dropdown
        $brands = CarBrand::orderBy('nama_merek')->get();
        $capacities = CarCapacity::orderBy('jumlah_orang')->get();

        // 🔹 Kirim semua data ke view
        return view('user.car.index', compact('cars', 'brands', 'capacities', 'suggestions'));
    }

    /**
     * 🚘 Tampilkan detail satu mobil
     */
public function show($id)
{
    
    $car = Car::with(['brand', 'capacity', 'photos'])->findOrFail($id);
    // 🔹 Tambahin 'photos' biar galeri ikut di-load

    // 🔹 Cek apakah mobil sedang disewa / menunggu pembayaran / dsb
    $rental = Rental::where('car_id', $car->car_id)
        ->whereIn('status_rental', ['verifikasi_diperlukan', 'menunggu', 'menunggu_pembayaran', 'berjalan'])
        ->latest()
        ->first();

    $isUnavailable = !!$rental; // true kalau sedang disewa

    return view('user.car.show', compact('car', 'isUnavailable', 'rental'));
}
}

