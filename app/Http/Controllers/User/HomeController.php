<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CarBrand;

class HomeController extends Controller
{
    public function index()
    {
        // Ambil semua merek
        $brands = CarBrand::all();

        // Ambil semua mobil (tanpa filter is_popular)
        $popularCars = Car::with(['brand', 'capacity'])->get();

        // Kirim data ke view
        return view('user.home.index', compact('brands', 'popularCars'));
    }
}
