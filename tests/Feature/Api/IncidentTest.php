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

it('sorts incidents by created at descending by default', function () {
    $incidents = Incident::factory(5)->sequence(
        ['created_at' => '2020-01-01'],
        ['created_at' => '2021-01-01'],
        ['created_at' => '2022-01-01'],
        ['created_at' => '2023-01-01'],
        ['created_at' => '2023-02-01'],
    )->create();

    $response = getJson('/status/api/incidents');

    $response->assertJsonPath('data.0.attributes.id', $incidents->last()->id);
    $response->assertJsonPath('data.1.attributes.id', $incidents->skip(3)->first()->id);
    $response->assertJsonPath('data.2.attributes.id', $incidents->skip(2)->first()->id);
    $response->assertJsonPath('data.3.attributes.id', $incidents->skip(1)->first()->id);
    $response->assertJsonPath('data.4.attributes.id', $incidents->first()->id);
});

it('can sort incidents by ID', function () {
    Incident::factory(5)->create();

    $response = getJson('/status/api/incidents?sort=id');

    $response->assertJsonPath('data.0.attributes.id', 1);
    $response->assertJsonPath('data.1.attributes.id', 2);
    $response->assertJsonPath('data.2.attributes.id', 3);
    $response->assertJsonPath('data.3.attributes.id', 4);
    $response->assertJsonPath('data.4.attributes.id', 5);
});

it('can sort incidents by name', function () {
    Incident::factory(5)->sequence(
        ['name' => 'c', 'created_at' => '2020-01-01'],
        ['name' => 'a', 'created_at' => '2021-01-01'],
        ['name' => 'b', 'created_at' => '2022-01-01'],
        ['name' => 'e', 'created_at' => '2023-01-01'],
        ['name' => 'd', 'created_at' => '2023-02-01'],
    )->create();

    $response = getJson('/status/api/incidents?sort=name');

    $response->assertJsonPath('data.0.attributes.id', 2);
    $response->assertJsonPath('data.1.attributes.id', 3);
    $response->assertJsonPath('data.2.attributes.id', 1);
    $response->assertJsonPath('data.3.attributes.id', 5);
    $response->assertJsonPath('data.4.attributes.id', 4);
});

it('can sort incidents by status', function () {
    Incident::factory(5)->sequence(
        ['status' => 3, 'created_at' => '2020-01-01'],
        ['status' => 1, 'created_at' => '2021-01-01'],
        ['status' => 2, 'created_at' => '2022-01-01'],
        ['status' => 1, 'created_at' => '2023-01-01'],
        ['status' => 4, 'created_at' => '2023-02-01'],
    )->create();

    $response = getJson('/status/api/incidents?sort=status');

    $response->assertJsonPath('data.0.attributes.id', 2);
    $response->assertJsonPath('data.1.attributes.id', 4);
    $response->assertJsonPath('data.2.attributes.id', 3);
    $response->assertJsonPath('data.3.attributes.id', 1);
    $response->assertJsonPath('data.4.attributes.id', 5);
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
    $response->assertJson([
        'data' => [
            'attributes' => [
                'name' => 'New Incident Occurred',
                'message' => 'Something went wrong.',
                'status' => [
                    'value' => 2,
                ],
            ],
        ],
    ]);
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

    $response->assertJson([
        'data' => [
            'attributes' => [
                'name' => 'Updated Incident',
                'status' => [
                    'value' => 1,
                ],
            ],
        ],
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
