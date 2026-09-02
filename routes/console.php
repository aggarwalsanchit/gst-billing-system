<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\DB;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Keep database alive every 5 minutes
Schedule::command('db:keep-alive')->everyFiveMinutes();

// Alternative: Direct database ping (no command needed)
Schedule::call(function () {
    try {
        DB::select('SELECT 1');
        \Log::info('Database keep-alive ping successful');
    } catch (\Exception $e) {
        \Log::error('Database keep-alive failed: ' . $e->getMessage());
    }
})->everyFiveMinutes();
