<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CarBrand;

class HomeController extends Controller
{
public function index()
{
    $brands = CarBrand::all();

    // Ambil 2 mobil terbaru untuk home
    $popularCars = Car::with(['brand', 'capacity'])
        ->latest()
        ->take(2)   // batasi jumlah di home
        ->get();

    return view('user.home.index', compact('brands', 'popularCars'));
}

}
