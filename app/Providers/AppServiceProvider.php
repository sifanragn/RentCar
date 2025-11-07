<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Payment;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Composer untuk semua layout admin
        View::composer(['layouts.admin.*', 'layouts.admin.partials.*'], function ($view) {
            $totalPendapatan = Payment::where('status_pembayaran', 'success')
            ->sum('total_bayar');


            $now = Carbon::now('Asia/Jakarta');

            $mingguIni = Payment::where('status_pembayaran', 'success')
                ->whereBetween('tanggal_bayar', [$now->copy()->subDays(7), $now])
                ->sum('total_bayar');

            $mingguLalu = Payment::where('status_pembayaran', 'success')
                ->whereBetween('tanggal_bayar', [$now->copy()->subDays(14), $now->copy()->subDays(7)])
                ->sum('total_bayar');

            $penambahan = $mingguIni - $mingguLalu;

            // 🔹 Tentukan teks dan arah perubahan
            $penambahanTeks = null;
            $statusPendapatan = 'stabil';

            if ($penambahan > 0) {
                $penambahanTeks = '+Rp' . number_format($penambahan, 0, ',', '.') . ' ↑';
                $statusPendapatan = 'naik';
            } elseif ($penambahan < 0) {
                $penambahanTeks = '-Rp' . number_format(abs($penambahan), 0, ',', '.') . ' ↓';
                $statusPendapatan = 'turun';
            } else {
                $penambahanTeks = 'Rp0 (stabil)';
                $statusPendapatan = 'stabil';
            }

            // kirim semua data ke view
            $view->with(compact('totalPendapatan', 'penambahanTeks', 'statusPendapatan'));
        });
    }
}
