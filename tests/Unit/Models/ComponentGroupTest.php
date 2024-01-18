<?php

use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Models\ComponentGroup;

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
