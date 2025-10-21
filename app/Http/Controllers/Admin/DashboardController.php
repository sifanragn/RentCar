<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Ambil beberapa mobil buat galeri
        $cars = Car::with('brand')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard.index', compact('cars'));
    }
}
