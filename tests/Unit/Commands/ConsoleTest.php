<?php

use Cachet\Database\Seeders\DatabaseSeeder;
use Cachet\Database\Seeders\DemoMetricSeeder;
use Illuminate\Console\Scheduling\Schedule;

it('registers a scheduled job', function () {
    // Resolve the schedule instance from the application container
    $schedule = app(Schedule::class);

    $events = collect($schedule->events())->keyBy('command')->keys()->all();

    // Build the expected scheduled commands
    $scheduledCommand = sprintf("'%s' 'artisan' db:seed --class='%s' --force",
        PHP_BINARY,
        DatabaseSeeder::class,
    );

    $demoMetricCommand = sprintf("'%s' 'artisan' db:seed --class='%s' --force",
        PHP_BINARY,
        DemoMetricSeeder::class,
    );

    expect($events)
        ->toContain($scheduledCommand)
        ->toContain($demoMetricCommand);
});
