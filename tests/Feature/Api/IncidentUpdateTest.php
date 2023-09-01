<?php

use Cachet\Models\Incident;
use Cachet\Models\IncidentUpdate;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\putJson;

it('can list incident updates', function () {
    $incident = Incident::factory()->hasIncidentUpdates(2)->create();

    $response = getJson("/status/api/incidents/{$incident->id}/updates");

    $response->assertOk();
    $response->assertJsonCount(2, 'data');
});

it('does not list more than 15 incident updates by default', function () {
    $incident = Incident::factory()->hasIncidentUpdates(20)->create();

    $response = getJson("/status/api/incidents/{$incident->id}/updates");

    $response->assertOk();
    $response->assertJsonCount(15, 'data');
});

it('can list more than 15 incident updates', function () {
    $incident = Incident::factory()->hasIncidentUpdates(20)->create();

    $response = getJson("/status/api/incidents/{$incident->id}/updates?per_page=18");

    $response->assertOk();
    $response->assertJsonCount(18, 'data');
});

it('can get an incident update', function () {
    $incidentUpdate = IncidentUpdate::factory()->forIncident()->create();

    $response = getJson("/status/api/incidents/{$incidentUpdate->incident_id}/updates/{$incidentUpdate->id}");

    $response->assertOk();
    $response->assertJsonFragment([
        'id' => $incidentUpdate->id,
    ]);
});

it('can get an incident update with incident', function () {
    $incidentUpdate = IncidentUpdate::factory()->forIncident()->create();

    $response = getJson("/status/api/incidents/{$incidentUpdate->incident_id}/updates/{$incidentUpdate->id}?include=incident");

    $response->assertOk();
    $response->assertJsonFragment([
        'id' => $incidentUpdate->incident_id,
    ]);
});

it('can update an incident update', function () {
    $incidentUpdate = IncidentUpdate::factory()->forIncident()->create();

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
    $incidentUpdate = IncidentUpdate::factory()->forIncident()->create();

    $response = deleteJson("/status/api/incidents/{$incidentUpdate->incident_id}/updates/{$incidentUpdate->id}");
    $response->assertNoContent();
    $this->assertDatabaseMissing('incident_updates', [
        'incident_id' => $incidentUpdate->incident_id,
    ]);
});

it('cannot delete an incident update from another incident', function () {
    $incident = Incident::factory()->create();
    $incidentUpdate = IncidentUpdate::factory()->forIncident()->create();

    $response = deleteJson("/status/api/incidents/{$incident->id}/updates/{$incidentUpdate->id}");
    $response->assertNotFound();
    $this->assertDatabaseHas('incident_updates', [
        'incident_id' => $incidentUpdate->incident_id,
    ]);
});
