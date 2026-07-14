<?php

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Models\Component;
use Cachet\Models\ComponentGroup;
use Cachet\Models\Incident;
use Cachet\Models\Meta;

it('has a group', function () {
    $component = Component::factory()->forGroup([
        'name' => 'my component group',
    ])->create();

    expect($component->group)
        ->toBeInstanceOf(ComponentGroup::class)
        ->name->toBe('my component group');
});

it('has incidents', function () {
    $component = Component::factory()->hasAttached(Incident::factory()->count(2), [
        'component_status' => ComponentStatusEnum::performance_issues,
    ])->create();

    expect($component->incidents)->toHaveCount(2);
});

it('has meta', function () {
    $component = Component::factory()->withMeta()->create();

    expect($component->metaValues())->toBe([
        'foo' => 'bar',
    ]);
});

it('keeps meta when soft deleted and purges it when force deleted', function () {
    $component = Component::factory()->withMeta()->create();

    $component->delete();

    expect(Meta::query()->where('meta_id', $component->id)->count())->toBe(1);

    $component->forceDelete();

    expect(Meta::query()->where('meta_id', $component->id)->count())->toBe(0);
});

it('can scope to disabled components', function () {
    Component::factory()->sequence([
        'enabled' => true,
    ], [
        'enabled' => false,
    ])->count(2)->create();

    expect(Component::query()->count())->toEqual(2)
        ->and(Component::query()->disabled()->count())->toEqual(1);
});

it('can scope to enabled components', function () {
    Component::factory()->sequence([
        'enabled' => true,
    ], [
        'enabled' => false,
    ])->count(2)->create();

    expect(Component::query()->count())->toEqual(2)
        ->and(Component::query()->enabled()->count())->toEqual(1);
});

it('can scope to components with a specific status', function () {
    Component::factory()->sequence([
        'status' => ComponentStatusEnum::operational,
    ], [
        'status' => ComponentStatusEnum::performance_issues,
    ])->count(2)->create();

    expect(Component::query()->count())->toEqual(2)
        ->and(Component::query()->status(ComponentStatusEnum::performance_issues)->count())->toEqual(1);
});

it('resolves the latest unresolved incident, not the oldest', function () {
    $component = Component::factory()->create(['status' => ComponentStatusEnum::operational]);

    $resolved = Incident::factory()->create([
        'status' => IncidentStatusEnum::fixed,
        'created_at' => now()->subDays(2),
    ]);
    $component->incidents()->attach($resolved, ['component_status' => ComponentStatusEnum::operational]);

    $ongoing = Incident::factory()->create([
        'status' => IncidentStatusEnum::investigating,
        'created_at' => now()->subHour(),
    ]);
    $component->incidents()->attach($ongoing, ['component_status' => ComponentStatusEnum::major_outage]);

    expect($component->latest_unresolved_incident->is($ongoing))->toBeTrue()
        ->and($component->latest_status)->toBe(ComponentStatusEnum::major_outage);
});

it('falls back to the component status without unresolved incidents', function () {
    $component = Component::factory()->create(['status' => ComponentStatusEnum::operational]);

    expect($component->latest_unresolved_incident)->toBeNull()
        ->and($component->latest_status)->toBe(ComponentStatusEnum::operational);
});
