<?php

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Mcp\CachetServer;
use Cachet\Mcp\Tools\ComponentGroups\CreateComponentGroup;
use Cachet\Mcp\Tools\ComponentGroups\DeleteComponentGroup;
use Cachet\Mcp\Tools\ComponentGroups\ListComponentGroups;
use Cachet\Mcp\Tools\ComponentGroups\UpdateComponentGroup;
use Cachet\Mcp\Tools\Components\CreateComponent;
use Cachet\Mcp\Tools\Components\DeleteComponent;
use Cachet\Mcp\Tools\Components\GetComponent;
use Cachet\Mcp\Tools\Components\ListComponents;
use Cachet\Mcp\Tools\Components\UpdateComponent;
use Cachet\Models\Component;
use Cachet\Models\ComponentGroup;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Workbench\App\User;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

it('lists components for guests', function () {
    Component::factory(2)->create();

    CachetServer::tool(ListComponents::class)
        ->assertOk()
        ->assertStructuredContent(fn (AssertableJson $json) => $json->has('data', 2)->etc());
});

it('filters components by status', function () {
    Component::factory()->create(['status' => ComponentStatusEnum::operational]);
    Component::factory()->create(['status' => ComponentStatusEnum::major_outage]);

    CachetServer::tool(ListComponents::class, ['status' => ComponentStatusEnum::major_outage->value])
        ->assertOk()
        ->assertStructuredContent(fn (AssertableJson $json) => $json->has('data', 1)->etc());
});

it('gets a component by id', function () {
    $component = Component::factory()->create(['name' => 'Documentation']);

    CachetServer::tool(GetComponent::class, ['id' => $component->id])
        ->assertOk()
        ->assertSee('Documentation');
});

it('returns an error for an unknown component', function () {
    CachetServer::tool(GetComponent::class, ['id' => 123])
        ->assertHasErrors(['Component [123] not found.']);
});

it('does not expose write tools without the ability', function () {
    Sanctum::actingAs(User::factory()->create());

    CachetServer::tool(CreateComponent::class, ['name' => 'API'])
        ->assertHasErrors();
});

it('creates a component', function () {
    Sanctum::actingAs(User::factory()->create(), ['components.manage']);

    CachetServer::tool(CreateComponent::class, [
        'name' => 'API',
        'status' => ComponentStatusEnum::operational->value,
    ])->assertOk()->assertSee('API');

    assertDatabaseHas('components', ['name' => 'API']);
});

it('validates component input', function () {
    Sanctum::actingAs(User::factory()->create(), ['components.manage']);

    CachetServer::tool(CreateComponent::class, [
        'name' => str_repeat('a', 300),
    ])->assertHasErrors();
});

it('updates a component', function () {
    Sanctum::actingAs(User::factory()->create(), ['components.manage']);

    $component = Component::factory()->create(['status' => ComponentStatusEnum::operational]);

    CachetServer::tool(UpdateComponent::class, [
        'id' => $component->id,
        'status' => ComponentStatusEnum::partial_outage->value,
    ])->assertOk();

    expect($component->fresh()->status)->toBe(ComponentStatusEnum::partial_outage);
});

it('deletes a component', function () {
    Sanctum::actingAs(User::factory()->create(), ['components.delete']);

    $component = Component::factory()->create();

    CachetServer::tool(DeleteComponent::class, ['id' => $component->id])
        ->assertOk()
        ->assertSee("Component [{$component->id}] deleted.");

    expect(Component::query()->find($component->id))->toBeNull();
});

it('lists component groups with their components', function () {
    $group = ComponentGroup::factory()->create(['name' => 'Core Services']);
    Component::factory()->create(['component_group_id' => $group->id]);

    CachetServer::tool(ListComponentGroups::class)
        ->assertOk()
        ->assertSee('Core Services');
});

it('creates a component group', function () {
    Sanctum::actingAs(User::factory()->create(), ['component-groups.manage']);

    CachetServer::tool(CreateComponentGroup::class, ['name' => 'Websites'])
        ->assertOk()
        ->assertSee('Websites');

    assertDatabaseHas('component_groups', ['name' => 'Websites']);
});

it('updates a component group', function () {
    Sanctum::actingAs(User::factory()->create(), ['component-groups.manage']);

    $group = ComponentGroup::factory()->create(['name' => 'Old Name']);

    CachetServer::tool(UpdateComponentGroup::class, [
        'id' => $group->id,
        'name' => 'New Name',
    ])->assertOk();

    expect($group->fresh()->name)->toBe('New Name');
});

it('deletes a component group', function () {
    Sanctum::actingAs(User::factory()->create(), ['component-groups.delete']);

    $group = ComponentGroup::factory()->create();

    CachetServer::tool(DeleteComponentGroup::class, ['id' => $group->id])
        ->assertOk();

    assertDatabaseMissing('component_groups', ['id' => $group->id]);
});
