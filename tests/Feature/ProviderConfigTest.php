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

it('only runs the beacon schedule when the beacon is enabled', function (bool $enabled) {
    config()->set('cachet.beacon', $enabled);

    $event = collect(app(Schedule::class)->events())
        ->first(fn ($event) => str_contains($event->command ?? '', 'cachet:beacon'));

    expect($event->filtersPass(app()))->toBe($enabled);
})->with([true, false]);

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
