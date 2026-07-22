<?php

use Cachet\CachetCoreServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

it('loads the package migrations by default', function () {
    $corePath = realpath(dirname((new ReflectionClass(CachetCoreServiceProvider::class))->getFileName()).'/../database/migrations');

    expect(collect(app('migrator')->paths())->map(fn (string $path) => realpath($path)))
        ->toContain($corePath);
});

it('registers the package schedules by default', function () {
    $commands = collect(app(Schedule::class)->events())
        ->map(fn ($event) => $event->command ?? '');

    expect($commands->filter(fn (string $command) => str_contains($command, 'cachet:beacon')))->not->toBeEmpty();
});
