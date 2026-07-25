<?php

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Models\Component;
use Cachet\Models\Incident;
use Cachet\Models\Schedule;

function publicIncident(ComponentStatusEnum $impact, Component $component, array $attributes = []): Incident
{
    $incident = Incident::factory()->create([
        'status' => IncidentStatusEnum::identified,
        'visible' => ResourceVisibilityEnum::guest,
        ...$attributes,
    ]);

    $incident->components()->attach($component->id, ['component_status' => $impact]);

    return $incident;
}

it('shows the most severe impact rather than the most recent', function () {
    $component = Component::factory()->create(['status' => ComponentStatusEnum::operational]);

    publicIncident(ComponentStatusEnum::major_outage, $component, ['created_at' => now()->subHour()]);
    publicIncident(ComponentStatusEnum::performance_issues, $component, ['created_at' => now()]);

    expect($component->fresh()->latest_status)->toBe(ComponentStatusEnum::major_outage);
});

it('links the badge to the incident whose impact is being shown', function () {
    $component = Component::factory()->create(['status' => ComponentStatusEnum::operational]);

    $worst = publicIncident(ComponentStatusEnum::major_outage, $component, ['created_at' => now()->subHour()]);
    publicIncident(ComponentStatusEnum::performance_issues, $component, ['created_at' => now()]);

    expect($component->fresh()->impacting_incident->is($worst))->toBeTrue();
});

it('keeps the baseline when an impact is less severe than it', function () {
    $component = Component::factory()->create(['status' => ComponentStatusEnum::major_outage]);

    publicIncident(ComponentStatusEnum::performance_issues, $component);

    expect($component->fresh()->latest_status)->toBe(ComponentStatusEnum::major_outage);
});

it('ignores impacts from incidents that are embargoed or hidden', function (array $attributes) {
    $component = Component::factory()->create(['status' => ComponentStatusEnum::operational]);

    publicIncident(ComponentStatusEnum::major_outage, $component, $attributes);

    expect($component->fresh()->latest_status)->toBe(ComponentStatusEnum::operational);
})->with([
    'embargoed' => [['published_at' => now()->addWeek()]],
    'hidden' => [['visible' => ResourceVisibilityEnum::hidden]],
    'authenticated only' => [['visible' => ResourceVisibilityEnum::authenticated]],
]);

it('ignores impacts from incidents that are resolved', function () {
    $component = Component::factory()->create(['status' => ComponentStatusEnum::operational]);

    publicIncident(ComponentStatusEnum::major_outage, $component, ['status' => IncidentStatusEnum::fixed]);

    expect($component->fresh()->latest_status)->toBe(ComponentStatusEnum::operational);
});

it('replaces the baseline while maintenance is in progress', function () {
    $component = Component::factory()->create(['status' => ComponentStatusEnum::major_outage]);

    $schedule = Schedule::factory()->create([
        'scheduled_at' => now()->subHour(),
        'completed_at' => now()->addHour(),
    ]);

    $schedule->components()->attach($component->id, [
        'component_status' => ComponentStatusEnum::under_maintenance,
    ]);

    expect($component->fresh()->latest_status)->toBe(ComponentStatusEnum::under_maintenance);
});

it('still surfaces an incident raised during maintenance', function () {
    $component = Component::factory()->create(['status' => ComponentStatusEnum::operational]);

    $schedule = Schedule::factory()->create([
        'scheduled_at' => now()->subHour(),
        'completed_at' => now()->addHour(),
    ]);

    $schedule->components()->attach($component->id, [
        'component_status' => ComponentStatusEnum::under_maintenance,
    ]);

    publicIncident(ComponentStatusEnum::major_outage, $component);

    expect($component->fresh()->latest_status)->toBe(ComponentStatusEnum::major_outage);
});

it('ignores maintenance windows that have not started or have finished', function (array $window) {
    $component = Component::factory()->create(['status' => ComponentStatusEnum::operational]);

    $schedule = Schedule::factory()->create($window);

    $schedule->components()->attach($component->id, [
        'component_status' => ComponentStatusEnum::under_maintenance,
    ]);

    expect($component->fresh()->latest_status)->toBe(ComponentStatusEnum::operational);
})->with([
    'upcoming' => [['scheduled_at' => now()->addHour(), 'completed_at' => now()->addHours(2)]],
    'completed' => [['scheduled_at' => now()->subHours(2), 'completed_at' => now()->subHour()]],
]);

it('resolves the same status whether or not the relations are eager loaded', function () {
    $component = Component::factory()->create(['status' => ComponentStatusEnum::operational]);

    publicIncident(ComponentStatusEnum::partial_outage, $component);

    $lazy = Component::query()->find($component->id);
    $eager = Component::query()->with(['unresolvedIncidents', 'activeMaintenance'])->find($component->id);

    expect($lazy->latest_status)
        ->toBe(ComponentStatusEnum::partial_outage)
        ->and($eager->latest_status)
        ->toBe(ComponentStatusEnum::partial_outage);
});
