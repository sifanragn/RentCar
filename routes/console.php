<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
use Illuminate\Support\Facades\Schedule;
// jalankan auto-expire setiap 30 menit
Schedule::call(function () {
    Artisan::call('route:call', [
        'uri' => '/payments/auto-expire',
        '--method' => 'GET',
    ]);
})->everyThirtyMinutes();

