<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CarBrand;

class HomeController extends Controller
{
    /**
     * Menampilkan halaman utama (home)
     * Berisi daftar brand mobil dan beberapa mobil populer/terbaru
     */
    public function index()
    {
        // ================= AMBIL DATA BRAND =================
        // Mengambil seluruh data merek mobil
        $brands = CarBrand::all();

        // ================= AMBIL DATA MOBIL =================
        // Mengambil 2 mobil terbaru beserta relasinya (brand & capacity)
        $popularCars = Car::with(['brand', 'capacity'])
            ->latest()     // urutkan berdasarkan data terbaru
            ->take(2)      // batasi hanya 2 mobil untuk ditampilkan di home
            ->get();

        // ================= TAMPILKAN VIEW =================
        // Mengirim data ke halaman user.home.index
        return view('user.home.index', compact('brands', 'popularCars'));
    }
}