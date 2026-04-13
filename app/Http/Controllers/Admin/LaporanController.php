<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Rental, Payment};
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    // Menampilkan halaman laporan dengan filter bulan, tahun, dan jenis pembayaran
    public function index(Request $request)
    {
        // ================= AMBIL FILTER =================
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $jenis = $request->jenis; // 'utama', 'tambahan', atau null

        // ================= AMBIL DATA RENTAL =================
        $rentals = Rental::with([
                'car.brand',
                'user',
                'payments' => function ($q) use ($jenis) {

                    // filter jenis pembayaran
                    if ($jenis === 'utama') {
                        $q->where('payment_type', 'main');
                    } elseif ($jenis === 'tambahan') {
                        $q->where('payment_type', 'charge');
                    }
                }
            ])
            // filter berdasarkan bulan
            ->when($bulan, fn($q) => $q->whereMonth('tanggal_mulai', $bulan))

            // filter berdasarkan tahun
            ->when($tahun, fn($q) => $q->whereYear('tanggal_mulai', $tahun))

            ->orderBy('tanggal_mulai', 'desc') // urutkan terbaru
            ->get()

            // hanya ambil rental yang punya pembayaran
            ->filter(fn($rental) => $rental->payments->isNotEmpty());

        // ================= HITUNG TOTAL PENDAPATAN =================
        $totalPendapatan = Payment::where('status_pembayaran', 'success')

            // filter jenis pembayaran
            ->whereIn('payment_type',
                $jenis === 'utama' ? ['main'] :
                ($jenis === 'tambahan' ? ['charge'] : ['main', 'charge'])
            )

            // filter bulan & tahun
            ->when($bulan, fn($q) => $q->whereMonth('created_at', $bulan))
            ->when($tahun, fn($q) => $q->whereYear('created_at', $tahun))

            ->sum('total_bayar'); // jumlahkan semua pembayaran

            return view('admin.laporan.index', compact(
                'rentals',
                'totalPendapatan',
                'bulan',
                'tahun',
                'jenis'
        ));
    }

    // Mencetak laporan dalam bentuk tampilan cetak (print)
    public function cetak(Request $request)
    {
        // ================= AMBIL FILTER =================
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $jenis = $request->jenis;

        // ================= AMBIL DATA RENTAL =================
        $rentals = Rental::with([
                'car.brand',
                'user',
                'payments',
                'invoice'
            ])
            ->when($bulan, fn($q) => $q->whereMonth('tanggal_mulai', $bulan))
            ->when($tahun, fn($q) => $q->whereYear('tanggal_mulai', $tahun))
            ->orderBy('tanggal_mulai', 'asc') // urutkan dari lama ke baru
            ->get();

        // ================= FILTER JENIS PEMBAYARAN =================
        if ($jenis === 'utama') {
            $rentals = $rentals->filter(fn($r) =>
                $r->payments->where('payment_type', 'main')->isNotEmpty()
            );
        } elseif ($jenis === 'tambahan') {
            $rentals = $rentals->filter(fn($r) =>
                $r->payments->where('payment_type', 'charge')->isNotEmpty()
            );
        }

        // ================= HITUNG TOTAL PENDAPATAN =================
        $totalPendapatan = Payment::where('status_pembayaran', 'success')
            ->whereIn('payment_type',
                $jenis === 'utama' ? ['main'] :
                ($jenis === 'tambahan' ? ['charge'] : ['main', 'charge'])
            )
            ->when($bulan, fn($q) => $q->whereMonth('created_at', $bulan))
            ->when($tahun, fn($q) => $q->whereYear('created_at', $tahun))
            ->sum('total_bayar');

        // ================= TANGGAL CETAK =================
        $tanggalCetak = Carbon::now('Asia/Jakarta')
            ->translatedFormat('d F Y'); // format tanggal Indonesia

        return view('admin.laporan.cetak', compact(
            'rentals',
            'totalPendapatan',
            'tanggalCetak',
            'bulan',
            'tahun',
            'jenis'
        ));
    }
}