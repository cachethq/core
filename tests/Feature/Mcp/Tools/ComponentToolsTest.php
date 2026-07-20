<?php

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\ResourceVisibilityEnum;
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

it('hides components in restricted groups from guests', function () {
    $hidden = ComponentGroup::factory()->create(['visible' => ResourceVisibilityEnum::hidden]);
    $internal = ComponentGroup::factory()->create(['visible' => ResourceVisibilityEnum::authenticated]);
    Component::factory()->create(['name' => 'Hidden Component', 'component_group_id' => $hidden->id]);
    Component::factory()->create(['name' => 'Internal Component', 'component_group_id' => $internal->id]);
    Component::factory()->create(['name' => 'Ungrouped Component']);

    CachetServer::tool(ListComponents::class)
        ->assertOk()
        ->assertSee('Ungrouped Component')
        ->assertDontSee('Hidden Component')
        ->assertDontSee('Internal Component');
});

it('shows components in authenticated-only groups to authenticated callers', function () {
    Sanctum::actingAs(User::factory()->create());

    $hidden = ComponentGroup::factory()->create(['visible' => ResourceVisibilityEnum::hidden]);
    $internal = ComponentGroup::factory()->create(['visible' => ResourceVisibilityEnum::authenticated]);
    Component::factory()->create(['name' => 'Hidden Component', 'component_group_id' => $hidden->id]);
    Component::factory()->create(['name' => 'Internal Component', 'component_group_id' => $internal->id]);

    CachetServer::tool(ListComponents::class)
        ->assertOk()
        ->assertSee('Internal Component')
        ->assertDontSee('Hidden Component');
});

it('hides disabled components from guests', function () {
    Component::factory()->create(['name' => 'Enabled Component', 'enabled' => true]);
    Component::factory()->create(['name' => 'Disabled Component', 'enabled' => false]);

    CachetServer::tool(ListComponents::class)
        ->assertOk()
        ->assertSee('Enabled Component')
        ->assertDontSee('Disabled Component');
});

it('lets authenticated callers list disabled components', function () {
    Sanctum::actingAs(User::factory()->create());

    Component::factory()->create(['name' => 'Enabled Component', 'enabled' => true]);
    Component::factory()->create(['name' => 'Disabled Component', 'enabled' => false]);

    CachetServer::tool(ListComponents::class, ['enabled' => false])
        ->assertOk()
        ->assertSee('Disabled Component')
        ->assertDontSee('Enabled Component');
});

it('does not reveal disabled components to guests by id', function () {
    $component = Component::factory()->create(['enabled' => false]);

    CachetServer::tool(GetComponent::class, ['id' => $component->id])
        ->assertHasErrors(["Component [{$component->id}] not found."]);
});

it('lets authenticated callers get disabled components by id', function () {
    Sanctum::actingAs(User::factory()->create());

    $component = Component::factory()->create(['name' => 'Disabled Component', 'enabled' => false]);

    CachetServer::tool(GetComponent::class, ['id' => $component->id])
        ->assertOk()
        ->assertSee('Disabled Component');
});

it('does not reveal components in restricted groups to guests by id', function () {
    $group = ComponentGroup::factory()->create(['visible' => ResourceVisibilityEnum::authenticated]);
    $component = Component::factory()->create(['component_group_id' => $group->id]);

    CachetServer::tool(GetComponent::class, ['id' => $component->id])
        ->assertHasErrors(["Component [{$component->id}] not found."]);
});

it('hides restricted component groups from guests', function () {
    ComponentGroup::factory()->create(['name' => 'Public Group', 'visible' => ResourceVisibilityEnum::guest]);
    ComponentGroup::factory()->create(['name' => 'Internal Group', 'visible' => ResourceVisibilityEnum::authenticated]);
    ComponentGroup::factory()->create(['name' => 'Hidden Group', 'visible' => ResourceVisibilityEnum::hidden]);

    CachetServer::tool(ListComponentGroups::class)
        ->assertOk()
        ->assertSee('Public Group')
        ->assertDontSee('Internal Group')
        ->assertDontSee('Hidden Group');
});

it('shows authenticated-only component groups to authenticated callers but never hidden ones', function () {
    Sanctum::actingAs(User::factory()->create());

    ComponentGroup::factory()->create(['name' => 'Internal Group', 'visible' => ResourceVisibilityEnum::authenticated]);
    ComponentGroup::factory()->create(['name' => 'Hidden Group', 'visible' => ResourceVisibilityEnum::hidden]);

    CachetServer::tool(ListComponentGroups::class)
        ->assertOk()
        ->assertSee('Internal Group')
        ->assertDontSee('Hidden Group');
});

it('hides disabled components inside groups from guests', function () {
    $group = ComponentGroup::factory()->create(['name' => 'Core Services']);
    Component::factory()->create(['name' => 'Disabled Grouped Component', 'component_group_id' => $group->id, 'enabled' => false]);

    CachetServer::tool(ListComponentGroups::class)
        ->assertOk()
        ->assertSee('Core Services')
        ->assertDontSee('Disabled Grouped Component');
});
