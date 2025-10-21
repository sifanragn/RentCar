<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use Carbon\Carbon;

class LaporanController extends Controller
{
    // Halaman utama laporan
    public function index()
    {
        $rentals = Rental::with(['car.brand', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        // total pendapatan dari yang selesai
        $totalPendapatan = $rentals->where('status_rental', 'selesai')->sum('total_biaya');

        return view('admin.laporan.index', compact('rentals', 'totalPendapatan'));
    }

    // Halaman cetak (PDF / print view)
    public function cetak()
    {
        $rentals = Rental::with(['car.brand', 'user'])
            ->where('status_rental', 'selesai')
            ->orderBy('created_at', 'desc')
            ->get();

        $totalPendapatan = $rentals->sum('total_biaya');
        $tanggalCetak = Carbon::now()->translatedFormat('d F Y');

        return view('admin.laporan.cetak', compact('rentals', 'totalPendapatan', 'tanggalCetak'));
    }
}
