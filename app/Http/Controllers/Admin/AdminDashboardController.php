<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Car; // 🔹 tambahkan ini
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // 🔹 Ambil data mobil beserta mereknya untuk galeri dashboard
        $cars = Car::with('brand')
            ->latest()
            ->take(10)
            ->get();

        // 🔹 Kirim semua data yang dibutuhkan ke view
        return view('admin.dashboard.index', [
            'admin_name' => session('admin_name'),
            'admin_role' => session('admin_role'),
            'cars' => $cars, // <— penting biar galeri tidak error
        ]);
    }
}
