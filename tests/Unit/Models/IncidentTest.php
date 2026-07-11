<?php

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Models\Component;
use Cachet\Models\Incident;
use Cachet\Models\Update;

it('can have multiple components', function () {
    $incident = Incident::factory()->create();

    $components = Component::factory(2)->create();
    $incident->components()->attach($components, ['component_status' => ComponentStatusEnum::performance_issues->value]);

    expect($incident->components)->toHaveCount(2)
        ->and($incident->status)->toBeInstanceOf(IncidentStatusEnum::class);
});

it('will set default guid', function () {
    $incident = Incident::factory()->state(['guid' => null])->create();

    expect($incident)->guid->not()->toBeNull();
});

it('has meta', function () {
    $incident = Incident::factory()->withMeta()->create();

    expect($incident->metaValues())->toBe([
        'foo' => 'bar',
    ]);
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
        ->and(Incident::stickied()->count())->toBe(1);
});

it('can scope to unresolved incidents', function () {
    Incident::factory()->sequence(
        ['status' => IncidentStatusEnum::investigating],
        ['status' => IncidentStatusEnum::identified],
        ['status' => IncidentStatusEnum::watching],
        ['status' => IncidentStatusEnum::fixed],
    )->count(4)->create();

    expect(Incident::query()->count())->toBe(4)
        ->and(Incident::unresolved()->count())->toBe(3);
});

it('resolves the latest status from the newest update when timestamps tie', function () {
    $incident = Incident::factory()->create(['status' => IncidentStatusEnum::investigating]);

    $timestamp = now()->startOfMinute();

    foreach ([IncidentStatusEnum::identified, IncidentStatusEnum::watching] as $status) {
        $update = new Update(['message' => 'Update.', 'status' => $status]);
        $update->created_at = $timestamp;
        $incident->updates()->save($update);
    }

    expect($incident->fresh()->latestStatus)->toBe(IncidentStatusEnum::watching);
});

it('falls back to its own status without updates', function () {
    $incident = Incident::factory()->create(['status' => IncidentStatusEnum::investigating]);

    expect($incident->latestStatus)->toBe(IncidentStatusEnum::investigating);
});
