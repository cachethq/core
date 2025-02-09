<?php

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Models\Component;
use Cachet\Models\ComponentGroup;
use Laravel\Sanctum\Sanctum;
use Workbench\App\User;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

it('can list components', function () {
    Component::factory(2)->create();

    $response = getJson('/status/api/components');

    $response->assertOk();
    $response->assertJsonCount(2, 'data');
});

it('does not list more than 15 components by default', function () {
    Component::factory(20)->create();

    $response = getJson('/status/api/components');

    $response->assertOk();
    $response->assertJsonCount(15, 'data');
});

it('can list more than 15 components', function () {
    Component::factory(20)->create();

    $response = getJson('/status/api/components?per_page=18');

    $response->assertOk();
    $response->assertJsonCount(18, 'data');
});

it('sorts components by id by default', function () {
    Component::factory(5)->create();

    $response = getJson('/status/api/components');

    $response->assertJsonPath('data.0.attributes.id', 1);
    $response->assertJsonPath('data.1.attributes.id', 2);
    $response->assertJsonPath('data.2.attributes.id', 3);
    $response->assertJsonPath('data.3.attributes.id', 4);
    $response->assertJsonPath('data.4.attributes.id', 5);
});

it('can sort components by name', function () {
    Component::factory(5)->sequence(
        ['name' => 'e'],
        ['name' => 'b'],
        ['name' => 'a'],
        ['name' => 'd'],
        ['name' => 'c'],
    )->create();

    $response = getJson('/status/api/components?sort=name');

    $response->assertJsonPath('data.0.attributes.id', 3);
    $response->assertJsonPath('data.1.attributes.id', 2);
    $response->assertJsonPath('data.2.attributes.id', 5);
    $response->assertJsonPath('data.3.attributes.id', 4);
    $response->assertJsonPath('data.4.attributes.id', 1);
});

it('can sort components by order', function () {
    Component::factory(5)->sequence(
        ['order' => 5],
        ['order' => 1],
        ['order' => 3],
        ['order' => 2],
        ['order' => 4],
    )->create();

    $response = getJson('/status/api/components?sort=order');

    $response->assertJsonPath('data.0.attributes.id', 2);
    $response->assertJsonPath('data.1.attributes.id', 4);
    $response->assertJsonPath('data.2.attributes.id', 3);
    $response->assertJsonPath('data.3.attributes.id', 5);
    $response->assertJsonPath('data.4.attributes.id', 1);
});

it('can filter components by name', function () {
    Component::factory(20)->create();
    $component = Component::factory()->create([
        'name' => 'Name to Filter by.',
    ]);

    $query = http_build_query([
        'filter' => [
            'name' => 'Name to Filter by.',
        ],
    ]);

    $response = getJson('/status/api/components?'.$query);

    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.attributes.id', $component->id);
});

it('can filter components by status', function () {
    Component::factory(19)->create([
        'status' => ComponentStatusEnum::performance_issues,
    ]);
    $component = Component::factory()->create([
        'status' => ComponentStatusEnum::major_outage,
    ]);

    $query = http_build_query([
        'filter' => [
            'status' => ComponentStatusEnum::major_outage->value,
        ],
    ]);

    $response = getJson('/status/api/components?'.$query);

    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.attributes.id', $component->id);
});

it('can filter components by enabled', function () {
    Component::factory(20)->disabled()->create();
    $component = Component::factory()->enabled()->create();

    $query = http_build_query([
        'filter' => ['enabled' => true],
    ]);

    $response = getJson('/status/api/components?'.$query);

    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.attributes.id', $component->id);
});

it('can filter components by disabled', function () {
    Component::factory(20)->disabled()->create();
    $component = Component::factory()->enabled()->create();

    $query = http_build_query([
        'filter' => ['enabled' => false],
    ]);

    $response = getJson('/status/api/components?per_page=25&'.$query);

    $response->assertJsonCount(20, 'data');
    $response->assertJsonMissing(['id' => $component->id]);
});

it('can get a component', function () {
    Component::factory(5)->create();
    $component = Component::factory()->create();

    $response = getJson('/status/api/components/'.$component->id);

    $response->assertOk();
    $response->assertJsonFragment([
        'id' => $component->id,
    ]);
});

it('can get a component with group', function () {
    Component::factory(5)->forGroup()->create();
    $component = Component::factory()->forGroup()->create();

    $response = getJson('/status/api/components/'.$component->id.'?include=group');

    $response->assertOk();
    $response->assertJsonFragment(['id' => $component->id]);
});

it('cannot create a component when not authenticated', function () {
    $response = postJson('/status/api/components', [
        'name' => 'Test',
        'description' => 'This is a new component, created by the API.',
        'order' => 2,
    ]);

    $response->assertUnauthorized();
});

it('cannot create a component without the token ability', function () {
    Sanctum::actingAs(User::factory()->create());

    $response = postJson('/status/api/components', [
        'name' => 'Test',
        'description' => 'This is a new component, created by the API.',
        'order' => 2,
    ]);

    $response->assertForbidden();
});

it('can create a component', function () {
    Sanctum::actingAs(User::factory()->create(), ['components.manage']);

    $response = postJson('/status/api/components', [
        'name' => 'Test',
        'description' => 'This is a new component, created by the API.',
        'order' => 2,
    ]);

    $response->assertCreated();
    $response->assertJsonFragment([
        'name' => 'Test',
    ]);
    $this->assertDatabaseHas('components', [
        'name' => 'Test',
        'description' => 'This is a new component, created by the API.',
        'order' => 2,
        'component_group_id' => null,
    ]);
});

it('can create a component and attach to a component group', function () {
    Sanctum::actingAs(User::factory()->create(), ['components.manage']);

    $componentGroup = ComponentGroup::factory()->create();
    $response = postJson('/status/api/components', [
        'name' => 'Test',
        'description' => 'This is a new component, created by the API.',
        'component_group_id' => $componentGroup->id,
    ]);

    $response->assertCreated();
    $response->assertJsonFragment([
        'name' => 'Test',
    ]);
    $this->assertDatabaseHas('components', [
        'name' => 'Test',
        'description' => 'This is a new component, created by the API.',
        'component_group_id' => $componentGroup->id,
    ]);
});

it('cannot attach a new component to a component group that does not exist', function () {
    Sanctum::actingAs(User::factory()->create(), ['components.manage']);

    $response = postJson('/status/api/components', [
        'name' => 'Test',
        'description' => 'This is a new component, created by the API.',
        'component_group_id' => 1,
    ]);

    $response->assertUnprocessable();
    $this->assertDatabaseMissing('components', [
        'name' => 'Test',
        'description' => 'This is a new component, created by the API.',
        'component_group_id' => 1,
    ]);
});

it('cannot update a component when not authenticated', function () {
    $component = Component::factory()->create([
        'order' => 10,
    ]);

    $response = putJson('/status/api/components/'.$component->id, [
        'name' => 'Updated Component Name',
        'description' => 'This is an updated component.',
    ]);

    $response->assertUnauthorized();
});

it('cannot update a component without the token ability', function () {
    Sanctum::actingAs(User::factory()->create());

    $component = Component::factory()->create([
        'order' => 10,
    ]);

    $response = putJson('/status/api/components/'.$component->id, [
        'name' => 'Updated Component Name',
        'description' => 'This is an updated component.',
    ]);

    $response->assertForbidden();
});

it('can update a component', function () {
    Sanctum::actingAs(User::factory()->create(), ['components.manage']);

    $component = Component::factory()->create([
        'order' => 10,
    ]);

    $response = putJson('/status/api/components/'.$component->id, [
        'name' => 'Updated Component Name',
        'description' => 'This is an updated component.',
    ]);

    $response->assertOk();
    $response->assertJsonFragment([
        'name' => 'Updated Component Name',
        'order' => 10,
    ]);
    $this->assertDatabaseHas('components', [
        'name' => 'Updated Component Name',
        'description' => 'This is an updated component.',
        'order' => 10,
    ]);
});

it('can update a component and attach it to a component group', function () {
    Sanctum::actingAs(User::factory()->create(), ['components.manage']);

    $componentGroup = ComponentGroup::factory()->create();
    $component = Component::factory()->create([
        'order' => 10,
    ]);

    $response = putJson('/status/api/components/'.$component->id, [
        'name' => 'Updated Component Name',
        'description' => 'This is an updated component.',
        'component_group_id' => $componentGroup->id,
    ]);

    $response->assertOk();
    $response->assertJsonFragment([
        'name' => 'Updated Component Name',
        'order' => 10,
    ]);
    $this->assertDatabaseHas('components', [
        'name' => 'Updated Component Name',
        'description' => 'This is an updated component.',
        'order' => 10,
        'component_group_id' => $componentGroup->id,
    ]);
});

it('cannot update a component and attach it to a component group that does not exist', function () {
    Sanctum::actingAs(User::factory()->create(), ['components.manage']);

    $component = Component::factory()->create();

    $response = putJson('/status/api/components/'.$component->id, [
        'name' => 'Updated Component Name',
        'description' => 'This is an updated component.',
        'component_group_id' => 1,
    ]);

    $response->assertUnprocessable();
    $this->assertDatabaseMissing('components', [
        'id' => $component->id,
        'component_group_id' => 1,
    ]);
});

it('cannot delete a component when not authenticated', function () {
    $component = Component::factory()->create();

    $response = deleteJson('/status/api/components/'.$component->id);

    $response->assertUnauthorized();
});

it('cannot delete a component without the token ability', function () {
    Sanctum::actingAs(User::factory()->create(), ['components.manage']);

    $component = Component::factory()->create();

    $response = deleteJson('/status/api/components/'.$component->id);

    $response->assertForbidden();
});

it('can delete a component', function () {
    Sanctum::actingAs(User::factory()->create(), ['components.delete']);

    $component = Component::factory()->create();

    $response = deleteJson('/status/api/components/'.$component->id);

    $response->assertNoContent();
    $this->assertSoftDeleted('components', [
        'id' => $component->id,
    ]);
});
