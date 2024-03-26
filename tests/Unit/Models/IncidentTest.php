<?php

use Cachet\Enums\IncidentStatusEnum;
use Cachet\Models\Incident;

it('can have multiple components', function () {
    $incident = Incident::factory()->hasComponents(2)->create();

    expect($incident->components)->toHaveCount(2);
});

it('can scope to a specific status', function () {
    Incident::factory()->sequence(
        ['status' => IncidentStatusEnum::investigating],
        ['status' => IncidentStatusEnum::identified],
        ['status' => IncidentStatusEnum::watching],
        ['status' => IncidentStatusEnum::fixed],
    )->count(4)->create();

    expect(Incident::query()->count())->toBe(4)
        ->and(Incident::query()->status(IncidentStatusEnum::investigating)->count())->toBe(1)
        ->and(Incident::query()->status(IncidentStatusEnum::identified)->count())->toBe(1)
        ->and(Incident::query()->status(IncidentStatusEnum::watching)->count())->toBe(1)
        ->and(Incident::query()->status(IncidentStatusEnum::fixed)->count())->toBe(1);
});

it('can scope to stickied incidents', function () {
    Incident::factory()->sequence(
        ['stickied' => false],
        ['stickied' => true],
    )->count(2)->create();

    expect(Incident::query()->count())->toBe(2)
        ->and(Incident::query()->stickied()->count())->toBe(1);
});

it('can scope to unresolved incidents', function () {
    Incident::factory()->sequence(
        ['status' => IncidentStatusEnum::investigating],
        ['status' => IncidentStatusEnum::identified],
        ['status' => IncidentStatusEnum::watching],
        ['status' => IncidentStatusEnum::fixed],
    )->count(4)->create();

    expect(Incident::query()->count())->toBe(4)
        ->and(Incident::query()->unresolved()->count())->toBe(3);
});
