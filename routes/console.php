<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Notifikasi invoice jatuh tempo — dikirim setiap hari pukul 08:00
Schedule::command('invoices:notify-due-dates')->dailyAt('08:00');
