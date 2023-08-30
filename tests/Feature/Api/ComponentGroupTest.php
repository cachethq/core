<?php

use Cachet\Models\ComponentGroup;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

it('can list component groups', function () {
    ComponentGroup::factory(2)->hasComponents(2)->create();

    $response = getJson('/status/api/component-groups');

    $response->assertOk();
    $response->assertJsonCount(2, 'data');
});

it('does not list more than 15 component groups by default', function () {
    ComponentGroup::factory(20)->hasComponents(2)->create();

    $response = getJson('/status/api/component-groups');

    $response->assertOk();
    $response->assertJsonCount(15, 'data');
});

it('can list more than 15 components', function () {
    ComponentGroup::factory(20)->create();

    $response = getJson('/status/api/component-groups?per_page=18');

    $response->assertOk();
    $response->assertJsonCount(18, 'data');
});

it('can get a component group', function () {
    $componentGroup = ComponentGroup::factory()->create();

    $response = getJson('/status/api/component-groups/'.$componentGroup->id);

    $response->assertOk();
    $response->assertJsonFragment([
        'id' => $componentGroup->id,
    ]);
});

it('can get a component group with components', function () {
    $componentGroup = ComponentGroup::factory()->hasComponents(2)->create();

    $response = getJson('/status/api/component-groups/'.$componentGroup->id.'?include=components');

    $response->assertOk();
    $response->assertJsonFragment(['id' => $componentGroup->id]);
});

it('can create a component group', function () {
    $response = postJson('/status/api/component-groups', [
        'name' => 'New Group',
    ]);

    $response->assertCreated();
    $response->assertJsonFragment([
        'name' => 'New Group',
    ]);
    $this->assertDatabaseHas('component_groups', [
        'name' => 'New Group',
    ]);
});
