<?php

use Cachet\Enums\ComponentGroupVisibilityEnum;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Models\Component;
use Cachet\Models\ComponentGroup;
use Cachet\Models\Incident;

it('can have components', function () {
    $group = ComponentGroup::factory()->hasComponents(2)->create();

    expect($group->components)->toHaveCount(2);
});

it('can scope to a specific visibility', function () {
    ComponentGroup::factory()->sequence(
        ['visible' => ResourceVisibilityEnum::authenticated],
        ['visible' => ResourceVisibilityEnum::guest],
        ['visible' => ResourceVisibilityEnum::hidden],
    )->count(3)->create();

    expect(ComponentGroup::query()->count())->toBe(3)
        ->and(ComponentGroup::query()->visibility(ResourceVisibilityEnum::authenticated)->count())->toBe(1)
        ->and(ComponentGroup::query()->visibility(ResourceVisibilityEnum::guest)->count())->toBe(1)
        ->and(ComponentGroup::query()->visibility(ResourceVisibilityEnum::hidden)->count())->toBe(1);
});

it('is always collapsed when collapsed option is set', function () {
    $group = ComponentGroup::factory()
        ->hasComponents(1)
        ->create(['collapsed' => ComponentGroupVisibilityEnum::collapsed]);

    expect($group->isExpanded())->toBeFalse();
});

it('is always expanded when expanded option is set', function () {
    $group = ComponentGroup::factory()
        ->hasComponents(1)
        ->create(['collapsed' => ComponentGroupVisibilityEnum::expanded]);

    expect($group->isExpanded())->toBeTrue();
});

it('is collapsed when collapsed_unless_incident is set and no incidents exist', function () {
    $group = ComponentGroup::factory()
        ->hasComponents(1)
        ->create(['collapsed' => ComponentGroupVisibilityEnum::collapsed_unless_incident]);

    expect($group->isExpanded())->toBeFalse();
});

it('is expanded when collapsed_unless_incident is set and an unresolved incident exists', function () {
    $group = ComponentGroup::factory()
        ->hasComponents(1)
        ->create(['collapsed' => ComponentGroupVisibilityEnum::collapsed_unless_incident]);

    $component = $group->components->first();
    $incident = Incident::factory()->create(['status' => IncidentStatusEnum::investigating]);
    $incident->components()->attach($component);

    expect($group->isExpanded())->toBeTrue();
});

it('is collapsed when collapsed_unless_incident is set and only resolved incidents exist', function () {
    $group = ComponentGroup::factory()
        ->hasComponents(1)
        ->create(['collapsed' => ComponentGroupVisibilityEnum::collapsed_unless_incident]);

    $component = $group->components->first();
    $incident = Incident::factory()->create(['status' => IncidentStatusEnum::fixed]);
    $incident->components()->attach($component);

    expect($group->isExpanded())->toBeFalse();
});

it('expands when an incident is started on a component', function () {
    $group = ComponentGroup::factory()
        ->hasComponents(1)
        ->create(['collapsed' => ComponentGroupVisibilityEnum::collapsed_unless_incident]);

    $component = $group->components->first();

    // Initially collapsed
    expect($group->isExpanded())->toBeFalse();

    // Create an incident and attach it to the component
    $incident = Incident::factory()->create(['status' => IncidentStatusEnum::investigating]);
    $incident->components()->attach($component);

    // Should now be expanded
    expect($group->fresh()->isExpanded())->toBeTrue();
});

it('collapses when all incidents are resolved', function () {
    $group = ComponentGroup::factory()
        ->hasComponents(1)
        ->create(['collapsed' => ComponentGroupVisibilityEnum::collapsed_unless_incident]);

    $component = $group->components->first();
    $incident = Incident::factory()->create(['status' => IncidentStatusEnum::investigating]);
    $incident->components()->attach($component);

    // Should be expanded with active incident
    expect($group->isExpanded())->toBeTrue();

    // Mark incident as fixed
    $incident->update(['status' => IncidentStatusEnum::fixed]);

    // Should now be collapsed
    expect($group->fresh()->isExpanded())->toBeFalse();
});

it('has active incident returns true for all unresolved statuses', function (IncidentStatusEnum $status) {
    $group = ComponentGroup::factory()
        ->hasComponents(1)
        ->create(['collapsed' => ComponentGroupVisibilityEnum::collapsed_unless_incident]);

    $component = $group->components->first();
    $incident = Incident::factory()->create(['status' => $status]);
    $incident->components()->attach($component);

    expect($group->hasActiveIncident())->toBeTrue();
})->with([
    'unknown' => IncidentStatusEnum::unknown,
    'investigating' => IncidentStatusEnum::investigating,
    'identified' => IncidentStatusEnum::identified,
    'watching' => IncidentStatusEnum::watching,
]);

it('has active incident returns false for fixed status', function () {
    $group = ComponentGroup::factory()
        ->hasComponents(1)
        ->create(['collapsed' => ComponentGroupVisibilityEnum::collapsed_unless_incident]);

    $component = $group->components->first();
    $incident = Incident::factory()->create(['status' => IncidentStatusEnum::fixed]);
    $incident->components()->attach($component);

    expect($group->hasActiveIncident())->toBeFalse();
});

it('expands when any component in the group has an active incident', function () {
    $group = ComponentGroup::factory()
        ->hasComponents(3)
        ->create(['collapsed' => ComponentGroupVisibilityEnum::collapsed_unless_incident]);

    $components = $group->components;

    // No incidents - should be collapsed
    expect($group->isExpanded())->toBeFalse();

    // Add incident to just one component
    $incident = Incident::factory()->create(['status' => IncidentStatusEnum::investigating]);
    $incident->components()->attach($components->first());

    // Should be expanded
    expect($group->fresh()->isExpanded())->toBeTrue();
});

it('resets component_group_id on components when the group is deleted', function () {
    $group = ComponentGroup::factory()->hasComponents(2)->create();
    $componentIds = $group->components->pluck('id');

    $group->delete();

    foreach ($componentIds as $id) {
        $this->assertDatabaseHas('components', [
            'id' => $id,
            'component_group_id' => null,
        ]);
    }
});

it('does not delete components when the group is deleted', function () {
    $group = ComponentGroup::factory()->hasComponents(2)->create();
    $componentIds = $group->components->pluck('id');

    $group->delete();

    foreach ($componentIds as $id) {
        $this->assertDatabaseHas('components', ['id' => $id]);
    }
});
