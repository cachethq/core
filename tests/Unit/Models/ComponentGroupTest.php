<?php

use Cachet\Models\ComponentGroup;

it('can have components', function () {
    $group = ComponentGroup::factory()->hasComponents(2)->create();

    expect($group->components)->toHaveCount(2);
});
