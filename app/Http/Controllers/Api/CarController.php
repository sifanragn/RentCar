<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Car;
use Illuminate\Http\Request;

class CarController extends Controller
{
    /**
     * 📋 Ambil semua data mobil
     */
    public function index()
    {
        $cars = Car::with('brand') // ambil relasi brand biar tampil juga
            ->orderBy('car_id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar semua mobil tersedia',
            'data' => $cars
        ]);
    }

    /**
     * 🔍 Detail 1 mobil berdasarkan ID
     */
    public function show($id)
    {
        $car = Car::with('brand')->find($id);

        if (!$car) {
            return response()->json([
                'success' => false,
                'message' => 'Mobil tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $car
        ]);
    }
}
