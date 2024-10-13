<?php

use Cachet\Enums\IncidentStatusEnum;
use Cachet\Models\Incident;
use Cachet\Models\Update;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\putJson;

it('can list incident updates', function () {
    $incident = Incident::factory()->hasUpdates(2)->create();

    $response = getJson("/status/api/incidents/{$incident->id}/updates");

    $response->assertOk();
    $response->assertJsonCount(2, 'data');
});

it('does not list more than 15 incident updates by default', function () {
    $incident = Incident::factory()->hasUpdates(20)->create();

    $response = getJson("/status/api/incidents/{$incident->id}/updates");

    $response->assertOk();
    $response->assertJsonCount(15, 'data');
});

it('can list more than 15 incident updates', function () {
    $incident = Incident::factory()->hasUpdates(20)->create();

    $response = getJson("/status/api/incidents/{$incident->id}/updates?per_page=18");

    $response->assertOk();
    $response->assertJsonCount(18, 'data');
});

it('sorts incident updates by id by default', function () {
    $incident = Incident::factory()->hasUpdates(20)->create();

    $response = getJson("/status/api/incidents/{$incident->id}/updates");

    $response->assertJsonPath('data.0.attributes.id', 1);
    $response->assertJsonPath('data.1.attributes.id', 2);
    $response->assertJsonPath('data.2.attributes.id', 3);
    $response->assertJsonPath('data.3.attributes.id', 4);
    $response->assertJsonPath('data.4.attributes.id', 5);
});

it('can sort incident updates by status', function () {
    $update = Update::factory(5)->sequence(
        ['status' => 3],
        ['status' => 1],
        ['status' => 4],
        ['status' => 2],
    )->create();

    dd($update->incident);

    $incident = Incident::query()->first();

    $response = getJson("/status/api/incidents/{$incident->id}/updates?sort=status");

    $response->assertJsonPath('data.0.attributes.id', 2);
    $response->assertJsonPath('data.1.attributes.id', 4);
    $response->assertJsonPath('data.2.attributes.id', 1);
    $response->assertJsonPath('data.4.attributes.id', 3);
});

it('can sort incident updates by created date', function () {
    Update::factory(4)->sequence(
        ['created_at' => '2022-01-01'],
        ['created_at' => '2020-01-01'],
        ['created_at' => '2023-01-01'],
        ['created_at' => '2021-01-01'],
    )->forIncident()->create();

    $incident = Incident::query()->first();

    $response = getJson("/status/api/incidents/{$incident->id}/updates?sort=created_at");

    $response->assertJsonPath('data.0.attributes.id', 2);
    $response->assertJsonPath('data.1.attributes.id', 4);
    $response->assertJsonPath('data.2.attributes.id', 1);
    $response->assertJsonPath('data.3.attributes.id', 3);
});

it('can filter incident updates by status', function () {
    $incident = Incident::factory()->hasUpdates(20)->create([
        'status' => IncidentStatusEnum::investigating,
    ]);

    $incidentUpdate = Update::factory()->for($incident)->create([
        'status' => IncidentStatusEnum::watching,
    ]);

    $incident = Incident::query()->first();

    $query = http_build_query([
        'filter' => [
            'status' => IncidentStatusEnum::watching->value,
        ],
    ]);

    $response = getJson("/status/api/incidents/{$incident->id}/updates?$query");

    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.attributes.id', $incidentUpdate->id);
});

it('can get an incident update', function () {
    $incidentUpdate = Update::factory()->forIncident()->create();

    $response = getJson("/status/api/incidents/{$incidentUpdate->incident_id}/updates/{$incidentUpdate->id}");

    $response->assertOk();
    $response->assertJsonFragment([
        'id' => $incidentUpdate->id,
    ]);
});

it('can get an incident update with incident', function () {
    $incidentUpdate = Update::factory()->forIncident()->create();

    $response = getJson("/status/api/incidents/{$incidentUpdate->incident_id}/updates/{$incidentUpdate->id}?include=incident");

    $response->assertOk();
    $response->assertJsonFragment([
        'id' => $incidentUpdate->incident_id,
    ]);
});

it('can update an incident update', function () {
    $incidentUpdate = Update::factory()->forIncident()->create();

    $data = [
        'message' => 'This is an updated message.',
    ];

    $response = putJson("/status/api/incidents/{$incidentUpdate->incident_id}/updates/{$incidentUpdate->id}", $data);

    $response->assertOk();
    $this->assertDatabaseHas('incident_updates', [
        'id' => $incidentUpdate->id,
        'status' => $incidentUpdate->status,
        ...$data,
    ]);
});

it('can delete an incident update', function () {
    $incidentUpdate = Update::factory()->forIncident()->create();

    $response = deleteJson("/status/api/incidents/{$incidentUpdate->incident_id}/updates/{$incidentUpdate->id}");
    $response->assertNoContent();
    $this->assertDatabaseMissing('incident_updates', [
        'incident_id' => $incidentUpdate->incident_id,
    ]);
});

it('cannot delete an incident update from another incident', function () {
    $incident = Incident::factory()->create();
    $incidentUpdate = Update::factory()->forIncident()->create();

    $response = deleteJson("/status/api/incidents/{$incident->id}/updates/{$incidentUpdate->id}");
    $response->assertNotFound();
    $this->assertDatabaseHas('incident_updates', [
        'incident_id' => $incidentUpdate->incident_id,
    ]);
});
