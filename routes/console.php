<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('earthquick:mail-retry --limit=25')
    ->everyFiveMinutes()
    ->when(fn () => config('communications.email_enabled'));
