<?php

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Models\Component;
use Cachet\Models\ComponentGroup;
use Cachet\Models\Incident;

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

it('casts meta to json', function () {
    $component = Component::factory()->withMeta()->create();

    expect($component)
        ->meta->toBe([
            'foo' => 'bar',
        ]);
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
