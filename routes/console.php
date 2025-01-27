<?php

use Cachet\Cachet;
use Cachet\Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Schedule;

$demoMode = fn () => Cachet::demoMode();

// Allows Cachet to be hosted in limited deployment environments,
// where we're unable to create custom scheduled jobs.
Schedule::command('db:seed', [
    '--class' => DatabaseSeeder::class,
    '--force' => true,
])
    ->everyThirtyMinutes()
    ->when($demoMode);
