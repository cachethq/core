<?php

use Cachet\Models\Component;

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

it('can get a component', function () {
    $component = Component::factory()->create();

    $response = getJson('/status/api/components/'.$component->id);

    $response->assertOk();
    $response->assertJsonFragment([
        'id' => $component->id,
    ]);
});

it('can get a component with group', function () {
    $component = Component::factory()->forGroup()->create();

    $response = getJson('/status/api/components/'.$component->id.'?include=group');

    $response->assertOk();
    $response->assertJsonFragment(['id' => $component->id]);
});

it('can create a component', function () {
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
    ]);
});

it('can update a component', function () {
    $component = Component::factory()->create();

    $response = putJson('/status/api/components/'.$component->id, [
        'name' => 'Updated Component Name',
        'description' => 'This is an updated component.',
    ]);

    $response->assertOk();
    $response->assertJsonFragment([
        'name' => 'Updated Component Name',
    ]);
    $this->assertDatabaseHas('components', [
        'name' => 'Updated Component Name',
        'description' => 'This is an updated component.',
    ]);
});

it('can delete a component', function () {
    $component = Component::factory()->create();

    $response = deleteJson('/status/api/components/'.$component->id);

    $response->assertNoContent();
    $this->assertSoftDeleted('components', [
        'id' => $component->id,
    ]);
});
