<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Artisan;

class Kernel extends ConsoleKernel
{
    /**
     * Daftar command yang tersedia (bisa kosong kalau pakai auto-discovery)
     */
    protected $commands = [
        //
    ];

    /**
     * Jadwalkan command (otomatis dijalankan oleh scheduler)
     */
    protected function schedule(Schedule $schedule): void
    {
        // 🧹 Jalankan auto-clean rental draft tiap 30 menit
        $schedule->command('rentals:clean-draft')
            ->everyThirtyMinutes()
            ->description('Auto hapus rental draft tiap 30 menit');

        // 💸 Jalankan auto-expire pembayaran tiap 30 menit
        $schedule->call(function () {
            Artisan::call('route:call', [
                'uri' => '/payments/auto-expire',
                '--method' => 'GET',
            ]);
        })->everyThirtyMinutes()
          ->description('Auto expire pembayaran tiap 30 menit');
    }

    /**
     * Daftarkan command yang harus di-load.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
