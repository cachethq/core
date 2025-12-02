<?php

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Models\Component;
use Cachet\Models\Incident;
use Cachet\Models\IncidentTemplate;
use Laravel\Sanctum\Sanctum;
use Workbench\App\User;

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

it('can filter incidents by name', function () {
    Incident::factory(20)->create();
    $incident = Incident::factory()->create([
        'name' => 'Name to filter by.',
    ]);

    $query = http_build_query([
        'filter' => [
            'name' => 'Name to filter by.',
        ],
    ]);

    $response = getJson('/status/api/incidents?'.$query);

    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.attributes.id', $incident->id);
});

it('can filter incidents by status', function () {
    Incident::factory(20)->create([
        'status' => IncidentStatusEnum::investigating,
    ]);
    $incident = Incident::factory()->create([
        'status' => IncidentStatusEnum::identified,
    ]);

    $query = http_build_query([
        'filter' => [
            'status' => IncidentStatusEnum::identified->value,
        ],
    ]);

    $response = getJson('/status/api/incidents?'.$query);

    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.attributes.id', $incident->id);
})->todo('This test needs to be aware of the computed status.');

it('can filter incidents by occurred at date', function () {
    Incident::factory(20)->create([
        'occurred_at' => '2019-01-01',
    ]);
    $incident = Incident::factory()->create([
        'occurred_at' => '2023-01-01',
    ]);

    $query = http_build_query([
        'filter' => [
            'occurred_at' => '2023-01-01',
        ],
    ]);

    $response = getJson('/status/api/incidents?'.$query);

    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.attributes.id', $incident->id);
});

it('can get an incident', function () {
    Incident::factory(5)->create();
    $incident = Incident::factory()->create();

    $response = getJson('/status/api/incidents/'.$incident->id);

    $response->assertOk();
    $response->assertJsonFragment([
        'id' => $incident->id,
    ]);
});

it('can get an incident with updates', function () {
    Incident::factory(5)->hasUpdates(2)->create();
    $incident = Incident::factory()->hasUpdates(2)->create();

    $response = getJson('/status/api/incidents/'.$incident->id.'?include=updates');

    $response->assertOk();
    $response->assertJsonFragment([
        'id' => $incident->id,
    ]);
});

it('cannot create an incident if not authenticated', function () {
    $response = postJson('/status/api/incidents', [
        'name' => 'New Incident Occurred',
        'message' => 'Something went wrong.',
        'status' => 2,
    ]);

    $response->assertUnauthorized();
});

it('cannot create an incident without the token ability', function () {
    Sanctum::actingAs(User::factory()->create());

    $response = postJson('/status/api/incidents', [
        'name' => 'New Incident Occurred',
        'message' => 'Something went wrong.',
        'status' => 2,
    ]);

    $response->assertForbidden();
});

it('can create an incident', function () {
    Sanctum::actingAs(User::factory()->create(), ['incidents.manage']);

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

it('can create an incident with components', function () {
    Sanctum::actingAs(User::factory()->create(), ['incidents.manage']);

    [$componentA, $componentB] = Component::factory(2)->create();

    $response = postJson('/status/api/incidents?include=components', [
        'name' => 'Incident With Components',
        'message' => 'Something went wrong.',
        'status' => IncidentStatusEnum::investigating->value,
        'components' => [
            ['id' => $componentA->id, 'status' => ComponentStatusEnum::partial_outage->value],
            ['id' => $componentB->id, 'status' => ComponentStatusEnum::major_outage->value],
        ],
    ]);

    $response->assertCreated();

    $incident = Incident::where('name', 'Incident With Components')->first();

    expect($incident)->not->toBeNull();

    $this->assertDatabaseHas('incident_components', [
        'incident_id' => $incident->id,
        'component_id' => $componentA->id,
        'component_status' => ComponentStatusEnum::partial_outage->value,
    ]);

    $this->assertDatabaseHas('incident_components', [
        'incident_id' => $incident->id,
        'component_id' => $componentB->id,
        'component_status' => ComponentStatusEnum::major_outage->value,
    ]);

    $included = collect($response->json('included'));

    expect($included)->toHaveCount(2);

    $componentAResource = $included->firstWhere('id', (string) $componentA->id);
    $componentBResource = $included->firstWhere('id', (string) $componentB->id);

    expect($componentAResource['attributes']['pivot']['component_status']['value'])
        ->toBe(ComponentStatusEnum::partial_outage->value);

    expect($componentBResource['attributes']['pivot']['component_status']['value'])
        ->toBe(ComponentStatusEnum::major_outage->value);
});

it('can create an incident with a template', function () {
    Sanctum::actingAs(User::factory()->create(), ['incidents.manage']);

    $incidentTemplate = IncidentTemplate::factory()->twig()->create();

    $response = postJson('/status/api/incidents', [
        'name' => 'New Incident Occurred',
        'template' => $incidentTemplate->slug,
        'status' => 2,
    ]);

    $response->assertCreated();
    $response->assertJson([
        'data' => [
            'attributes' => [
                'name' => 'New Incident Occurred',
                'message' => "Hey,\n\nA new incident has been reported:\n\nName: New Incident Occurred\n",
                'status' => [
                    'value' => 2,
                ],
            ],
        ],
    ]);
});

it('cannot create an incident with bad data', function (array $payload) {
    Sanctum::actingAs(User::factory()->create(), ['incidents.manage']);

    $response = postJson('/status/api/incidents', $payload);

    $response->assertUnprocessable();
    $response->assertInvalid(array_keys($response->json('errors')));
})->with([
    fn () => ['name' => null, 'message' => null],
    fn () => ['name' => 'New Incident', 'message' => null, 'status' => 999],
    fn () => ['name' => 'New Incident', 'template' => 123, 'status' => 999],
]);

it('cannot update an incident if not authenticated', function () {
    $incident = Incident::factory()->create();

    $response = putJson('/status/api/incidents/'.$incident->id, [
        'name' => 'New Incident Occurred',
        'message' => 'Something went wrong.',
        'status' => 2,
    ]);

    $response->assertUnauthorized();
});

it('cannot update an incident without the token ability', function () {
    Sanctum::actingAs(User::factory()->create());

    $incident = Incident::factory()->create();

    $response = putJson('/status/api/incidents/'.$incident->id, [
        'name' => 'New Incident Occurred',
        'message' => 'Something went wrong.',
        'status' => 2,
    ]);

    $response->assertForbidden();
});

it('can update an incident', function () {
    Sanctum::actingAs(User::factory()->create(), ['incidents.manage']);

    $incident = Incident::factory()->create();

    $response = putJson('/status/api/incidents/'.$incident->id, [
        'name' => 'New Incident Occurred',
        'message' => 'Something went wrong.',
        'status' => 2,
    ]);

    $response->assertOk();
});

it('can update an incident while passing null data', function (array $payload) {
    Sanctum::actingAs(User::factory()->create(), ['incidents.manage']);

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
    Sanctum::actingAs(User::factory()->create(), ['incidents.manage']);

    $incident = Incident::factory()->create();

    $response = putJson('/status/api/incidents/'.$incident->id, $payload);

    $response->assertUnprocessable();
    $response->assertInvalid(array_keys($response->json('errors')));
})->with([
    fn () => ['name' => null, 'message' => null],
    fn () => ['name' => 'New Incident', 'message' => null, 'status' => 999],
]);

it('cannot delete an incident if not authenticated', function () {
    $incident = Incident::factory()->create();

    $response = deleteJson('/status/api/incidents/'.$incident->id);

    $response->assertUnauthorized();
});

it('cannot delete an incident without the token ability', function () {
    Sanctum::actingAs(User::factory()->create());

    $incident = Incident::factory()->create();

    $response = deleteJson('/status/api/incidents/'.$incident->id);

    $response->assertForbidden();
});

it('can delete an incident', function () {
    Sanctum::actingAs(User::factory()->create(), ['incidents.delete']);

    $incident = Incident::factory()->create();

    $response = deleteJson('/status/api/incidents/'.$incident->id);

    $response->assertNoContent();
});
