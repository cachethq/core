<?php

use Cachet\Enums\ComponentGroupVisibilityEnum;
use Cachet\Enums\ResourceOrderColumnEnum;
use Cachet\Enums\ResourceOrderDirectionEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Models\Component;
use Cachet\Models\ComponentGroup;
use Laravel\Sanctum\Sanctum;
use Workbench\App\User;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

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

it('sorts component groups by id by default', function () {
    $groups = ComponentGroup::factory(5)->hasComponents(2)->create();

    $response = getJson('/status/api/component-groups');

    $response->assertSeeInOrder(
        $groups->sortBy('id')->take(5)->pluck('id')->all()
    );
});

it('can get a component group', function () {
    ComponentGroup::factory(5)->create();
    $componentGroup = ComponentGroup::factory()->create();

    $response = getJson('/status/api/component-groups/'.$componentGroup->id);

    $response->assertOk();
    $response->assertJsonFragment([
        'id' => $componentGroup->id,
    ]);
});

it('can get a component group with components', function () {
    ComponentGroup::factory(5)->hasComponents(1)->create();
    $componentGroup = ComponentGroup::factory()->hasComponents(2)->create();

    $response = getJson('/status/api/component-groups/'.$componentGroup->id.'?include=components');

    $response->assertOk();
    $response->assertJsonFragment(['id' => $componentGroup->id]);
});

it('can filter component groups by meta', function () {
    ComponentGroup::factory(5)->create();
    $group = ComponentGroup::factory()->create();
    $group->syncMeta(['region' => 'eu-west']);

    $query = http_build_query(['filter' => ['meta' => ['region' => 'eu-west']]]);

    $response = getJson('/status/api/component-groups?'.$query);

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.attributes.id', $group->id);
});

it('can include meta on a component group', function () {
    $group = ComponentGroup::factory()->create();
    $group->syncMeta(['region' => 'eu-west', 'priority' => 3, 'critical' => true]);

    $response = getJson('/status/api/component-groups/'.$group->id.'?include=meta');

    $response->assertOk();
    $response->assertJsonPath('data.attributes.meta', [
        'region' => 'eu-west',
        'priority' => 3,
        'critical' => true,
    ]);
});

it('does not include meta on a component group by default', function () {
    $group = ComponentGroup::factory()->create();
    $group->syncMeta(['region' => 'eu-west']);

    $response = getJson('/status/api/component-groups/'.$group->id);

    $response->assertOk();
    $response->assertJsonMissingPath('data.attributes.meta');
});

it('cannot create a component group when not authenticated', function () {
    $response = postJson('/status/api/component-groups', [
        'name' => 'New Group',
    ]);

    $response->assertUnauthorized();
});

it('can create a component group with meta', function () {
    Sanctum::actingAs(User::factory()->create(), ['component-groups.manage']);

    $response = postJson('/status/api/component-groups', [
        'name' => 'New Group',
        'meta' => ['region' => 'eu-west', 'priority' => 3, 'critical' => true],
    ]);

    $response->assertCreated();
    expect(ComponentGroup::query()->firstWhere('name', 'New Group')->metaValues())
        ->toBe(['region' => 'eu-west', 'priority' => 3, 'critical' => true]);
});

it('cannot create a component group without the token ability', function () {
    Sanctum::actingAs(User::factory()->create());

    $response = postJson('/status/api/component-groups', [
        'name' => 'New Group',
    ]);

    $response->assertForbidden();
});

it('can create a component group without components', function () {
    Sanctum::actingAs(User::factory()->create(), ['component-groups.manage']);

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

it('can create a component group with a collapsed state', function () {
    Sanctum::actingAs(User::factory()->create(), ['component-groups.manage']);

    $response = postJson('/status/api/component-groups', [
        'name' => 'New Group',
        'collapsed' => ComponentGroupVisibilityEnum::collapsed_unless_incident->value,
    ]);

    $response->assertCreated();
    $this->assertDatabaseHas('component_groups', [
        'name' => 'New Group',
        'collapsed' => ComponentGroupVisibilityEnum::collapsed_unless_incident->value,
    ]);
});

it('cannot create a component group with an invalid collapsed state', function () {
    Sanctum::actingAs(User::factory()->create(), ['component-groups.manage']);

    $response = postJson('/status/api/component-groups', [
        'name' => 'New Group',
        'collapsed' => 99,
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors('collapsed');
});

it('can create a component group and attach existing components', function () {
    Sanctum::actingAs(User::factory()->create(), ['component-groups.manage']);

    $components = Component::factory()->create();

    $response = postJson('/status/api/component-groups', [
        'name' => 'New Group',
        'components' => $components->pluck('id')->values()->all(),
    ]);

    $response->assertCreated();
    $response->assertJsonFragment([
        'name' => 'New Group',
    ]);
    $this->assertDatabaseHas('component_groups', [
        'name' => 'New Group',
    ]);
    $this->assertDatabaseHas('components', [
        'component_group_id' => $response->json('data.id'),
    ]);
});

it('cannot update a component group when not authenticated', function () {
    $componentGroup = ComponentGroup::factory()->create();

    $response = putJson('/status/api/component-groups/'.$componentGroup->id, [
        'name' => 'Updated Component Group Name',
    ]);

    $response->assertUnauthorized();
});

it('cannot update a component group without the token ability', function () {
    Sanctum::actingAs(User::factory()->create());

    $componentGroup = ComponentGroup::factory()->create();

    $response = putJson('/status/api/component-groups/'.$componentGroup->id, [
        'name' => 'Updated Component Group Name',
    ]);

    $response->assertForbidden();
});

it('can update a component group', function () {
    Sanctum::actingAs(User::factory()->create(), ['component-groups.manage']);

    $componentGroup = ComponentGroup::factory()->create();

    $response = putJson('/status/api/component-groups/'.$componentGroup->id, [
        'name' => 'Updated Component Group Name',
    ]);

    $response->assertOk();
    $response->assertJsonFragment([
        'name' => 'Updated Component Group Name',
    ]);
    $this->assertDatabaseHas('component_groups', [
        'name' => 'Updated Component Group Name',
    ]);
});

it('can update a component group collapsed state', function () {
    Sanctum::actingAs(User::factory()->create(), ['component-groups.manage']);

    $componentGroup = ComponentGroup::factory()->create([
        'collapsed' => ComponentGroupVisibilityEnum::expanded->value,
    ]);

    $response = putJson('/status/api/component-groups/'.$componentGroup->id, [
        'collapsed' => ComponentGroupVisibilityEnum::collapsed_unless_incident->value,
    ]);

    $response->assertOk();
    $this->assertDatabaseHas('component_groups', [
        'id' => $componentGroup->id,
        'collapsed' => ComponentGroupVisibilityEnum::collapsed_unless_incident->value,
    ]);
});

it('cannot update a component group with an invalid collapsed state', function () {
    Sanctum::actingAs(User::factory()->create(), ['component-groups.manage']);

    $componentGroup = ComponentGroup::factory()->create();

    $response = putJson('/status/api/component-groups/'.$componentGroup->id, [
        'collapsed' => 99,
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors('collapsed');
});

it('can update a component group with components', function () {
    Sanctum::actingAs(User::factory()->create(), ['component-groups.manage']);

    $components = Component::factory()->count(3)->create();
    $componentGroup = ComponentGroup::factory()->create();

    $response = putJson('/status/api/component-groups/'.$componentGroup->id, [
        'name' => 'Updated Component Group Name',
        'components' => $components->pluck('id')->values()->all(),
    ]);

    $response->assertOk();
    $response->assertJsonFragment([
        'name' => 'Updated Component Group Name',
    ]);
    $this->assertDatabaseHas('component_groups', [
        'name' => 'Updated Component Group Name',
    ]);
    $this->assertDatabaseHas('components', [
        'component_group_id' => $response->json('data.id'),
    ]);
});

it('cannot delete a component group when not authenticated', function () {
    $componentGroup = ComponentGroup::factory()->create();

    $response = deleteJson('/status/api/component-groups/'.$componentGroup->id);

    $response->assertUnauthorized();
});

it('cannot delete a component group without the token ability', function () {
    Sanctum::actingAs(User::factory()->create());

    $componentGroup = ComponentGroup::factory()->create();

    $response = deleteJson('/status/api/component-groups/'.$componentGroup->id);

    $response->assertForbidden();
});

it('can delete a component group', function () {
    Sanctum::actingAs(User::factory()->create(), ['component-groups.delete']);

    $componentGroup = ComponentGroup::factory()->create();

    $response = deleteJson('/status/api/component-groups/'.$componentGroup->id);

    $response->assertNoContent();
    $this->assertDatabaseMissing('component_groups', [
        'id' => $componentGroup->id,
    ]);
});

it('updates components group id when a group is deleted', function () {
    Sanctum::actingAs(User::factory()->create(), ['component-groups.delete']);

    $componentGroup = ComponentGroup::factory()->hasComponents(2)->create();

    $response = deleteJson('/status/api/component-groups/'.$componentGroup->id);

    $response->assertNoContent();
    $this->assertDatabaseMissing('component_groups', [
        'id' => $componentGroup->id,
    ]);
    $this->assertDatabaseHas('components', [
        'id' => 1,
        'component_group_id' => null,
    ]);
    $this->assertDatabaseHas('components', [
        'id' => 2,
        'component_group_id' => null,
    ]);
});

it('exposes the order column and direction in the resource', function () {
    $componentGroup = ComponentGroup::factory()
        ->orderedBy(ResourceOrderColumnEnum::Name, ResourceOrderDirectionEnum::Desc)
        ->create();

    $response = getJson('/status/api/component-groups/'.$componentGroup->id);

    $response->assertOk();
    $response->assertJsonFragment([
        'order_column' => ResourceOrderColumnEnum::Name->value,
        'order_direction' => ResourceOrderDirectionEnum::Desc->value,
    ]);
});

it('can create a component group with a manual order column', function () {
    Sanctum::actingAs(User::factory()->create(), ['component-groups.manage']);

    $response = postJson('/status/api/component-groups', [
        'name' => 'New Group',
        'order_column' => ResourceOrderColumnEnum::Manual->value,
    ]);

    $response->assertCreated();
    $this->assertDatabaseHas('component_groups', [
        'name' => 'New Group',
        'order_column' => ResourceOrderColumnEnum::Manual->value,
        'order_direction' => null,
    ]);
});

it('can create a component group with an order column and direction', function () {
    Sanctum::actingAs(User::factory()->create(), ['component-groups.manage']);

    $response = postJson('/status/api/component-groups', [
        'name' => 'New Group',
        'order_column' => ResourceOrderColumnEnum::Name->value,
        'order_direction' => ResourceOrderDirectionEnum::Asc->value,
    ]);

    $response->assertCreated();
    $this->assertDatabaseHas('component_groups', [
        'name' => 'New Group',
        'order_column' => ResourceOrderColumnEnum::Name->value,
        'order_direction' => ResourceOrderDirectionEnum::Asc->value,
    ]);
});

it('requires an order direction when the order column is not manual', function () {
    Sanctum::actingAs(User::factory()->create(), ['component-groups.manage']);

    $response = postJson('/status/api/component-groups', [
        'name' => 'New Group',
        'order_column' => ResourceOrderColumnEnum::Status->value,
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors('order_direction');
});

it('does not require an order direction when the order column is manual', function () {
    Sanctum::actingAs(User::factory()->create(), ['component-groups.manage']);

    $response = postJson('/status/api/component-groups', [
        'name' => 'New Group',
        'order_column' => ResourceOrderColumnEnum::Manual->value,
    ]);

    $response->assertCreated();
});

it('cannot create a component group with an invalid order column', function () {
    Sanctum::actingAs(User::factory()->create(), ['component-groups.manage']);

    $response = postJson('/status/api/component-groups', [
        'name' => 'New Group',
        'order_column' => 'not-a-column',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors('order_column');
});

it('cannot create a component group with an invalid order direction', function () {
    Sanctum::actingAs(User::factory()->create(), ['component-groups.manage']);

    $response = postJson('/status/api/component-groups', [
        'name' => 'New Group',
        'order_column' => ResourceOrderColumnEnum::Name->value,
        'order_direction' => 'sideways',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors('order_direction');
});

it('can update a component group order column and direction', function () {
    Sanctum::actingAs(User::factory()->create(), ['component-groups.manage']);

    $componentGroup = ComponentGroup::factory()->create([
        'order_column' => ResourceOrderColumnEnum::Manual,
    ]);

    $response = putJson('/status/api/component-groups/'.$componentGroup->id, [
        'order_column' => ResourceOrderColumnEnum::LastUpdated->value,
        'order_direction' => ResourceOrderDirectionEnum::Desc->value,
    ]);

    $response->assertOk();
    $this->assertDatabaseHas('component_groups', [
        'id' => $componentGroup->id,
        'order_column' => ResourceOrderColumnEnum::LastUpdated->value,
        'order_direction' => ResourceOrderDirectionEnum::Desc->value,
    ]);
});

it('can update an unrelated attribute without supplying an order direction', function () {
    Sanctum::actingAs(User::factory()->create(), ['component-groups.manage']);

    $componentGroup = ComponentGroup::factory()->create([
        'order_column' => ResourceOrderColumnEnum::Manual,
    ]);

    $response = putJson('/status/api/component-groups/'.$componentGroup->id, [
        'name' => 'Renamed Group',
    ]);

    $response->assertOk();
    $this->assertDatabaseHas('component_groups', [
        'id' => $componentGroup->id,
        'name' => 'Renamed Group',
    ]);
});

it('does not list component groups hidden from guests', function () {
    ComponentGroup::factory()->create(['visible' => ResourceVisibilityEnum::guest]);
    ComponentGroup::factory()->create(['visible' => ResourceVisibilityEnum::authenticated]);
    ComponentGroup::factory()->create(['visible' => ResourceVisibilityEnum::hidden]);

    $response = getJson('/status/api/component-groups');

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
});

it('lists authenticated component groups to authenticated users but never hidden ones', function () {
    Sanctum::actingAs(User::factory()->create());

    ComponentGroup::factory()->create(['visible' => ResourceVisibilityEnum::guest]);
    ComponentGroup::factory()->create(['visible' => ResourceVisibilityEnum::authenticated]);
    ComponentGroup::factory()->create(['visible' => ResourceVisibilityEnum::hidden]);

    $response = getJson('/status/api/component-groups');

    $response->assertOk();
    $response->assertJsonCount(2, 'data');
});

it('does not show a hidden component group to guests', function () {
    $componentGroup = ComponentGroup::factory()->create(['visible' => ResourceVisibilityEnum::hidden]);

    $response = getJson('/status/api/component-groups/'.$componentGroup->id);

    $response->assertNotFound();
});

it('shows an authenticated component group to authenticated users', function () {
    Sanctum::actingAs(User::factory()->create());

    $componentGroup = ComponentGroup::factory()->create(['visible' => ResourceVisibilityEnum::authenticated]);

    $response = getJson('/status/api/component-groups/'.$componentGroup->id);

    $response->assertOk();
    $response->assertJsonPath('data.attributes.id', $componentGroup->id);
});

it('does not include disabled components in a group to guests', function () {
    $group = ComponentGroup::factory()->create(['visible' => ResourceVisibilityEnum::guest]);
    Component::factory()->enabled()->create(['component_group_id' => $group->id]);
    Component::factory()->disabled()->create(['component_group_id' => $group->id]);

    $response = getJson('/status/api/component-groups/'.$group->id.'?include=components');

    $response->assertOk();
    $response->assertJsonCount(1, 'included');
});

it('includes disabled components in a group to authenticated users', function () {
    Sanctum::actingAs(User::factory()->create());

    $group = ComponentGroup::factory()->create(['visible' => ResourceVisibilityEnum::guest]);
    Component::factory()->enabled()->create(['component_group_id' => $group->id]);
    Component::factory()->disabled()->create(['component_group_id' => $group->id]);

    $response = getJson('/status/api/component-groups/'.$group->id.'?include=components');

    $response->assertOk();
    $response->assertJsonCount(2, 'included');
});

it('lists authenticated component groups to callers presenting a bearer token', function () {
    $user = User::factory()->create();
    $token = $user->createToken('api')->plainTextToken;

    ComponentGroup::factory()->create(['visible' => ResourceVisibilityEnum::guest]);
    ComponentGroup::factory()->create(['visible' => ResourceVisibilityEnum::authenticated]);
    ComponentGroup::factory()->create(['visible' => ResourceVisibilityEnum::hidden]);

    $response = getJson('/status/api/component-groups', ['Authorization' => 'Bearer '.$token]);

    $response->assertOk();
    $response->assertJsonCount(2, 'data');
});
