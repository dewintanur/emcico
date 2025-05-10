<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\KirimNotifikasiCheckout;

// Menambahkan command 'inspire'
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Menjadwalkan command KirimNotifikasiCheckout setiap 5 menit
Schedule::command('app:kirim-notifikasi-checkout')->everyFiveMinutes();

