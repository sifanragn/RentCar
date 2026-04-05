<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;   // ← TAMBAHKAN INI
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;


class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
            Paginator::useTailwind();

        // FORCE HTTPS kalau kamu akses dari ngrok
        if (request()->isSecure() || str_contains(env('APP_URL'), 'ngrok')) {
            URL::forceScheme('https');
        }

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

            $view->with(compact('totalPendapatan', 'penambahanTeks', 'statusPendapatan'));
        });
    }
}
