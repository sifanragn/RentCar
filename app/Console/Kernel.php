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
          // ✅ Reminder H-1 rental dimulai
$schedule->call(function () {
    $rentals = \App\Models\Rental::with(['user', 'car.brand'])
        ->whereDate('tanggal_mulai', now()->addDay()->toDateString())
        ->where('status_rental', 'berjalan')
        ->get();

    foreach ($rentals as $r) {
        \App\Helpers\Whatsapp::send(
            $r->user->no_hp,
            "⏰ *Pengingat Rental Mobil Besok!*

Halo *{$r->user->nama_lengkap}*, ini adalah pengingat bahwa rental mobil Anda akan dimulai besok 🚗✨

📆 Tanggal Mulai: {$r->tanggal_mulai}
🚘 Mobil: {$r->car->brand->nama_merek} {$r->car->model}
📍 Metode Pickup: " . (
                $r->metode_pickup == 'ambil_sendiri'
                ? "Ambil di Kantor Rental"
                : "Antar ke Alamat Anda"
            ) . "

Terima kasih telah mempercayai layanan kami 🙏😊"
        );
    }
})->dailyAt('09:00')->description('Reminder rental H-1 via WhatsApp');

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
