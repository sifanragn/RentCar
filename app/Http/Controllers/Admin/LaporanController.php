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
        $jenis = $request->jenis; // 'utama', 'tambahan', atau null (semua)

        // 🔹 Query dasar rentals dengan relasi
        $rentals = Rental::with(['car.brand', 'user', 'payments'])
            ->when($bulan && $tahun, function ($q) use ($bulan, $tahun) {
                $q->whereMonth('tanggal_mulai', $bulan)
                  ->whereYear('tanggal_mulai', $tahun);
            })
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        // 🔹 Filter jenis kuitansi (utama / tambahan)
        if ($jenis === 'utama') {
            $rentals = $rentals->filter(fn($r) =>
                $r->payments->where('payment_type', 'main')->isNotEmpty()
            );
        } elseif ($jenis === 'tambahan') {
            $rentals = $rentals->filter(fn($r) =>
                $r->payments->where('payment_type', 'charge')->isNotEmpty()
            );
        }

        // 💰 Hitung total pendapatan sesuai filter
        $totalPendapatan = Payment::where('status_pembayaran', 'success')
            ->whereIn('payment_type', $jenis === 'utama' ? ['main'] :
                ($jenis === 'tambahan' ? ['charge'] : ['main', 'charge']))
            ->whereHas('rental', function ($q) use ($bulan, $tahun) {
                $q->whereIn('status_rental', ['selesai', 'selesai_dengan_charge'])
                  ->when($bulan && $tahun, function ($r) use ($bulan, $tahun) {
                      $r->whereMonth('tanggal_mulai', $bulan)
                        ->whereYear('tanggal_mulai', $tahun);
                  });
            })
            ->sum('total_bayar');

        return view('admin.laporan.index', compact('rentals', 'totalPendapatan', 'bulan', 'tahun', 'jenis'));
    }

    public function cetak(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $jenis = $request->jenis;

        $rentals = Rental::with(['car.brand', 'user', 'payments', 'invoice'])
            ->when($bulan && $tahun, function ($q) use ($bulan, $tahun) {
                $q->whereMonth('tanggal_mulai', $bulan)
                  ->whereYear('tanggal_mulai', $tahun);
            })
            ->orderBy('tanggal_mulai', 'asc')
            ->get();

        // Filter jenis kuitansi juga di cetak
        if ($jenis === 'utama') {
            $rentals = $rentals->filter(fn($r) =>
                $r->payments->where('payment_type', 'main')->isNotEmpty()
            );
        } elseif ($jenis === 'tambahan') {
            $rentals = $rentals->filter(fn($r) =>
                $r->payments->where('payment_type', 'charge')->isNotEmpty()
            );
        }

        // 💰 Total pendapatan sesuai jenis filter
        $totalPendapatan = Payment::where('status_pembayaran', 'success')
            ->whereIn('payment_type', $jenis === 'utama' ? ['main'] :
                ($jenis === 'tambahan' ? ['charge'] : ['main', 'charge']))
            ->whereHas('rental', function ($q) use ($bulan, $tahun) {
                $q->whereIn('status_rental', ['selesai', 'selesai_dengan_charge'])
                  ->when($bulan && $tahun, function ($r) use ($bulan, $tahun) {
                      $r->whereMonth('tanggal_mulai', $bulan)
                        ->whereYear('tanggal_mulai', $tahun);
                  });
            })
            ->sum('total_bayar');

        $tanggalCetak = Carbon::now('Asia/Jakarta')->translatedFormat('d F Y');

        return view('admin.laporan.cetak', compact('rentals', 'totalPendapatan', 'tanggalCetak', 'bulan', 'tahun', 'jenis'));
    }
}
