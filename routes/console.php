<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule cleanup for pending orders older than 10 minutes
// Run every minute
Schedule::command('orders:cleanup-pending --minutes=10')->everyFiveMinutes();

// Check pending Midtrans payments (fallback when webhook is unavailable)
// Queries Midtrans API directly for orders still PENDING within 24h
Schedule::command('payments:check-pending')->everyFiveMinutes();
