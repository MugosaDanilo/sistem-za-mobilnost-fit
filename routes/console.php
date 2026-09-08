<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Dnevna sinhronizacija matičnih predmeta sa studentske platforme
if (config('platforma.enabled')) {
    Schedule::command('platforma:sync-predmeti')->dailyAt('03:00');
}
