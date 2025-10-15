<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CarBrand;
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    public function index()
    {
        // Ambil 6 mobil terbaru
        $cars = Car::with('brand')
            ->where('status', 'tersedia')
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        // Hitung jumlah mobil yang tersedia
        $countCars = Car::where('status', 'tersedia')->count();

        // Ambil daftar merek untuk filter di dashboard
        $brands = CarBrand::orderBy('nama_merek')->get();

        // Ambil 6 mobil populer (contohnya pakai random dulu)
        $popularCars = Car::with(['brand', 'capacity'])
            ->where('status', 'tersedia')
            ->inRandomOrder()
            ->limit(6)
            ->get();

        return view('user.dashboard.index', compact('cars', 'countCars', 'brands', 'popularCars'));
    }
}
