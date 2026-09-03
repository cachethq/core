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

it('checks components every minute without overlapping', function () {
    $componentCheck = collect(app(Schedule::class)->events())
        ->first(fn ($event) => str_contains($event->command ?? '', 'cachet:check'));

    expect($componentCheck)
        ->not->toBeNull()
        ->expression->toBe('* * * * *')
        ->withoutOverlapping->toBeTrue()
        ->expiresAt->toBe(5);
});

it('prevents every package schedule from overlapping', function () {
    $events = collect(app(Schedule::class)->events());

    expect($events)->not->toBeEmpty();

    $events->each(fn ($event) => expect($event->withoutOverlapping)->toBeTrue());
});

it('configures safe image upload defaults', function () {
    expect(config('cachet.uploads'))
        ->disk->toBe('public')
        ->max_size->toBe(1024)
        ->image_mime_types->toBe([
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
        ]);
});
