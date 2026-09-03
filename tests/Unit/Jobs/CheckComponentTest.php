<?php

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Jobs\CheckComponent;
use Cachet\Models\Component;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

it('queues only one outstanding check per component', function () {
    $component = Component::factory()->checked()->create();
    $job = new CheckComponent($component);

    expect($job)->toBeInstanceOf(ShouldBeUnique::class)
        ->and($job->uniqueId())->toBe((string) $component->getKey());
});

it('skips a stale check after monitoring is disabled', function () {
    Http::fake();

    $component = Component::factory()->checked()->create();
    $component->update(['checked' => false]);

    dispatch_sync(new CheckComponent($component));

    Http::assertNothingSent();
    expect($component->checks()->count())->toBe(0);
});

it('records an operational check for a healthy component', function () {
    Http::fake([
        '*' => Http::response('OK', 200),
    ]);

    $component = Component::factory()->checked()->create();

    dispatch_sync(new CheckComponent($component));

    $component->refresh();

    expect($component->status)->toBe(ComponentStatusEnum::operational)
        ->and($component->checked_at)->not->toBeNull()
        ->and($component->checks)->toHaveCount(1);

    $check = $component->checks->first();

    expect($check->status)->toBe(ComponentStatusEnum::operational)
        ->and($check->successful)->toBeTrue()
        ->and($check->response_code)->toBe(200);
});

it('records a partial outage for a 4xx response', function () {
    Http::fake([
        '*' => Http::response('Not Found', 404),
    ]);

    $component = Component::factory()->checked()->create();

    dispatch_sync(new CheckComponent($component));

    $component->refresh();

    expect($component->status)->toBe(ComponentStatusEnum::partial_outage)
        ->and($component->checks()->first()->successful)->toBeFalse()
        ->and($component->checks()->first()->response_code)->toBe(404);
});

it('records a major outage for a 5xx response', function () {
    Http::fake([
        '*' => Http::response('Server Error', 500),
    ]);

    $component = Component::factory()->checked()->create();

    dispatch_sync(new CheckComponent($component));

    expect($component->refresh()->status)->toBe(ComponentStatusEnum::major_outage);
});

it('records a partial outage when the connection fails', function () {
    Http::fake(function () {
        throw new ConnectionException('Connection timed out');
    });

    $component = Component::factory()->checked()->create();

    dispatch_sync(new CheckComponent($component));

    $component->refresh();

    expect($component->status)->toBe(ComponentStatusEnum::partial_outage)
        ->and($component->checks)->toHaveCount(1)
        ->and($component->checks->first()->successful)->toBeFalse();
});
