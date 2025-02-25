<?php

use Cachet\Database\Seeders\DatabaseSeeder;
use Illuminate\Console\Scheduling\Schedule;

it('registers a scheduled job', function () {
    // Resolve the schedule instance from the application container
    $schedule = app(Schedule::class);

    $events = collect($schedule->events())->keyBy('command')->keys()->all();

    // Build the expected scheduled command
    $scheduledCommand = sprintf("'%s' 'artisan' db:seed --class='%s' --force",
        PHP_BINARY,
        DatabaseSeeder::class,
    );

    expect($events)
        ->toContain($scheduledCommand);
});
