<?php

use Cachet\Models\Incident;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

it('can list incidents', function () {
    Incident::factory(2)->create();

    $response = getJson('/status/api/incidents');

    $response->assertOk();
    $response->assertJsonCount(2, 'data');
});

it('does not list more than 15 incidents by default', function () {
    Incident::factory(20)->create();

    $response = getJson('/status/api/incidents');

    $response->assertOk();
    $response->assertJsonCount(15, 'data');
});

it('can list more than 15 incidents', function () {
    Incident::factory(20)->create();

    $response = getJson('/status/api/incidents?per_page=18');

    $response->assertOk();
    $response->assertJsonCount(18, 'data');
});

it('can get an incident', function () {
    $incident = Incident::factory()->create();

    $response = getJson('/status/api/incidents/'.$incident->id);

    $response->assertOk();
    $response->assertJsonFragment([
        'id' => $incident->id,
    ]);
});

it('can get an incident with updates', function () {
    $incident = Incident::factory()->hasUpdates(2)->create();

    $response = getJson('/status/api/incidents/'.$incident->id.'?include=updates');

    $response->assertOk();
    $response->assertJsonFragment([
        'id' => $incident->id,
    ]);
});

it('can create an incident', function () {
    $response = postJson('/status/api/incidents', [
        'name' => 'New Incident Occurred',
        'message' => 'Something went wrong.',
    ]);

    $response->assertCreated();
    $response->assertJsonFragment([
        'name' => 'New Incident Occurred',
    ]);
});
