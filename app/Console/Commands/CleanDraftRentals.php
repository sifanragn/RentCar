<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Rental;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CleanDraftRentals extends Command
{
    /**
     * Nama command (jalankan di terminal)
     */
    protected $signature = 'rentals:clean-draft';

    /**
     * Deskripsi (buat dibaca di "php artisan list")
     */
    protected $description = 'Hapus otomatis rental dengan status draft yang lebih dari 30 menit';

    public function handle()
{
    $limit = Carbon::now()->subMinutes(30);

    // Hapus semua draft yang sudah 30 menit ATAU expired_at lewat
    $deleted = Rental::where('status_rental', 'draft')
        ->where(function ($q) use ($limit) {
            $q->where('created_at', '<=', $limit)
              ->orWhere(function ($query) {
                  $query->whereNotNull('expired_at')
                        ->where('expired_at', '<', now());
              });
        })
        ->delete();

    if ($deleted > 0) {
        Log::info("🧹 Auto-clean: {$deleted} draft rentals dihapus otomatis.");
        $this->info("🧹 {$deleted} draft rentals deleted.");
    } else {
        $this->info("✅ Tidak ada draft yang perlu dihapus.");
    }
}

}
