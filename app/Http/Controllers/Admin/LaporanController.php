<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Rental, Payment};
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $jenis = $request->jenis; // 'utama', 'tambahan', atau null

        // 🔹 Ambil data rental dengan relasi & filter jenis pembayaran
        $rentals = Rental::with(['car.brand', 'user', 'payments' => function ($q) use ($jenis) {
                if ($jenis === 'utama') {
                    $q->where('payment_type', 'main');
                } elseif ($jenis === 'tambahan') {
                    $q->where('payment_type', 'charge');
                }
            }])
            ->when($bulan, fn($q) => $q->whereMonth('tanggal_mulai', $bulan))
            ->when($tahun, fn($q) => $q->whereYear('tanggal_mulai', $tahun))
            ->orderBy('tanggal_mulai', 'desc')
            ->get()
            ->filter(fn($rental) => $rental->payments->isNotEmpty());

        // 💰 Hitung total pendapatan sesuai filter
        $totalPendapatan = Payment::where('status_pembayaran', 'success')
            ->whereIn('payment_type', $jenis === 'utama' ? ['main'] :
                ($jenis === 'tambahan' ? ['charge'] : ['main', 'charge']))
            ->when($bulan, fn($q) => $q->whereMonth('created_at', $bulan))
            ->when($tahun, fn($q) => $q->whereYear('created_at', $tahun))
            ->sum('total_bayar');

        return view('admin.laporan.index', compact(
            'rentals', 'totalPendapatan', 'bulan', 'tahun', 'jenis'
        ));
    }

    public function cetak(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $jenis = $request->jenis;

        $rentals = Rental::with(['car.brand', 'user', 'payments', 'invoice'])
            ->when($bulan, fn($q) => $q->whereMonth('tanggal_mulai', $bulan))
            ->when($tahun, fn($q) => $q->whereYear('tanggal_mulai', $tahun))
            ->orderBy('tanggal_mulai', 'asc')
            ->get();

        // 🔹 Filter jenis kuitansi
        if ($jenis === 'utama') {
            $rentals = $rentals->filter(fn($r) =>
                $r->payments->where('payment_type', 'main')->isNotEmpty()
            );
        } elseif ($jenis === 'tambahan') {
            $rentals = $rentals->filter(fn($r) =>
                $r->payments->where('payment_type', 'charge')->isNotEmpty()
            );
        }

        // 💰 Total pendapatan
        $totalPendapatan = Payment::where('status_pembayaran', 'success')
            ->whereIn('payment_type', $jenis === 'utama' ? ['main'] :
                ($jenis === 'tambahan' ? ['charge'] : ['main', 'charge']))
            ->when($bulan, fn($q) => $q->whereMonth('created_at', $bulan))
            ->when($tahun, fn($q) => $q->whereYear('created_at', $tahun))
            ->sum('total_bayar');

        $tanggalCetak = Carbon::now('Asia/Jakarta')->translatedFormat('d F Y');

        return view('admin.laporan.cetak', compact(
            'rentals', 'totalPendapatan', 'tanggalCetak', 'bulan', 'tahun', 'jenis'
        ));
    }
}
