<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
// jalankan auto-expire setiap 30 menit
Schedule::call(function () {
    Artisan::call('route:call', [
        'uri' => '/payments/auto-expire',
        '--method' => 'GET',
    ]);
})->everyThirtyMinutes();
Schedule::command('rentals:clean-draft')
    ->everyThirtyMinutes()
    ->description('Auto hapus rental draft tiap 30 menit');

