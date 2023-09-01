<?php

use Cachet\Models\Incident;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

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
    $incident = Incident::factory()->hasIncidentUpdates(2)->create();

    $response = getJson('/status/api/incidents/'.$incident->id.'?include=inidcent_updates');

    $response->assertOk();
    $response->assertJsonFragment([
        'id' => $incident->id,
    ]);
});

it('can create an incident', function () {
    $response = postJson('/status/api/incidents', $payload = [
        'name' => 'New Incident Occurred',
        'message' => 'Something went wrong.',
        'status' => 2,
    ]);

    $response->assertCreated();
    $response->assertJsonFragment($payload);
});

it('cannot create an incident with bad data', function (array $payload) {
    $response = postJson('/status/api/incidents', $payload);

    $response->assertUnprocessable();
    $response->assertInvalid(array_keys($response->json('errors')));
})->with([
    fn () => ['name' => null, 'message' => null],
    fn () => ['name' => 'New Incident', 'message' => null, 'status' => 999],
]);

it('can update an incident', function () {
    $incident = Incident::factory()->create();

    $response = putJson('/status/api/incidents/'.$incident->id, [
        'name' => 'New Incident Occurred',
        'message' => 'Something went wrong.',
        'status' => 2,
    ]);

    $response->assertOk();
});

it('can update an incident while passing null data', function (array $payload) {
    $incident = Incident::factory()->create();

    $response = putJson('/status/api/incidents/'.$incident->id, $payload);

    $response->assertJsonFragment([
        'name' => 'Updated Incident',
        'status' => 1,
    ]);
})->with([
    fn () => ['name' => 'Updated Incident', 'status' => 1],
]);

it('cannot update an incident with bad data', function (array $payload) {
    $incident = Incident::factory()->create();

    $response = putJson('/status/api/incidents/'.$incident->id, $payload);

    $response->assertUnprocessable();
    $response->assertInvalid(array_keys($response->json('errors')));
})->with([
    fn () => ['name' => null, 'message' => null],
    fn () => ['name' => 'New Incident', 'message' => null, 'status' => 999],
]);

it('can delete an incident', function () {
    $incident = Incident::factory()->create();

    $response = deleteJson('/status/api/incidents/'.$incident->id);

    $response->assertNoContent();
});
