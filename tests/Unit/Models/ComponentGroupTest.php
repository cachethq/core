<?php

use Cachet\Enums\ComponentGroupVisibilityEnum;
use Cachet\Models\ComponentGroup;

it('can have components', function () {
    $group = ComponentGroup::factory()->hasComponents(2)->create();

    expect($group->components)->toHaveCount(2);
});

it('can scope to a specific visibility', function () {
    ComponentGroup::factory()->sequence(
        ['visible' => ComponentGroupVisibilityEnum::expanded],
        ['visible' => ComponentGroupVisibilityEnum::collapsed],
        ['visible' => ComponentGroupVisibilityEnum::collapsed_unless_incident],
    )->count(3)->create();

    expect(ComponentGroup::query()->count())->toBe(3)
        ->and(ComponentGroup::query()->visibility(ComponentGroupVisibilityEnum::expanded)->count())->toBe(1)
        ->and(ComponentGroup::query()->visibility(ComponentGroupVisibilityEnum::collapsed)->count())->toBe(1)
        ->and(ComponentGroup::query()->visibility(ComponentGroupVisibilityEnum::collapsed_unless_incident)->count())->toBe(1);
});
