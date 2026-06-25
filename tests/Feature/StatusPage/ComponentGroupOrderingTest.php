<?php

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\ResourceOrderColumnEnum;
use Cachet\Enums\ResourceOrderDirectionEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Models\Component;
use Cachet\Models\ComponentGroup;

function orderingGroup(ResourceOrderColumnEnum $column, ?ResourceOrderDirectionEnum $direction = ResourceOrderDirectionEnum::Asc): ComponentGroup
{
    return ComponentGroup::factory()
        ->orderedBy($column, $direction)
        ->create(['visible' => ResourceVisibilityEnum::guest]);
}

it('orders a group\'s components by name ascending', function () {
    $group = orderingGroup(ResourceOrderColumnEnum::Name, ResourceOrderDirectionEnum::Asc);

    Component::factory()->for($group, 'group')->create(['name' => 'CharlieSvc']);
    Component::factory()->for($group, 'group')->create(['name' => 'AlphaSvc']);
    Component::factory()->for($group, 'group')->create(['name' => 'BravoSvc']);

    $this->get(route('cachet.status-page'))
        ->assertOk()
        ->assertSeeInOrder(['AlphaSvc', 'BravoSvc', 'CharlieSvc']);
});

it('orders a group\'s components by name descending', function () {
    $group = orderingGroup(ResourceOrderColumnEnum::Name, ResourceOrderDirectionEnum::Desc);

    Component::factory()->for($group, 'group')->create(['name' => 'CharlieSvc']);
    Component::factory()->for($group, 'group')->create(['name' => 'AlphaSvc']);
    Component::factory()->for($group, 'group')->create(['name' => 'BravoSvc']);

    $this->get(route('cachet.status-page'))
        ->assertOk()
        ->assertSeeInOrder(['CharlieSvc', 'BravoSvc', 'AlphaSvc']);
});

it('orders a group\'s components by status ascending', function () {
    $group = orderingGroup(ResourceOrderColumnEnum::Status, ResourceOrderDirectionEnum::Asc);

    Component::factory()->for($group, 'group')->create(['name' => 'OutageSvc', 'status' => ComponentStatusEnum::major_outage]);
    Component::factory()->for($group, 'group')->create(['name' => 'OkaySvc', 'status' => ComponentStatusEnum::operational]);

    $this->get(route('cachet.status-page'))
        ->assertOk()
        ->assertSeeInOrder(['OkaySvc', 'OutageSvc']);
});

it('falls back to the manual order column when ordering manually', function () {
    $group = orderingGroup(ResourceOrderColumnEnum::Manual, null);

    Component::factory()->for($group, 'group')->create(['name' => 'ThirdSvc', 'order' => 2]);
    Component::factory()->for($group, 'group')->create(['name' => 'FirstSvc', 'order' => 0]);
    Component::factory()->for($group, 'group')->create(['name' => 'SecondSvc', 'order' => 1]);

    $this->get(route('cachet.status-page'))
        ->assertOk()
        ->assertSeeInOrder(['FirstSvc', 'SecondSvc', 'ThirdSvc']);
});
