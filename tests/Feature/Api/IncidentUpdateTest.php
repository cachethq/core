<?php

use Cachet\Enums\IncidentStatusEnum;
use Cachet\Models\Incident;
use Cachet\Models\Update;
use Illuminate\Database\Eloquent\Relations\Relation;
use Laravel\Sanctum\Sanctum;
use Workbench\App\User;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
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
    $incident = Incident::factory()->create();
    Update::factory()->count(5)->sequence(
        ['status' => 3],
        ['status' => 1],
        ['status' => 4],
        ['status' => 2],
    )->create([
        'updateable_type' => 'incident',
        'updateable_id' => $incident->id,
    ]);

    $response = getJson("/status/api/incidents/{$incident->id}/updates?sort=status");

    $response->assertJsonPath('data.0.attributes.id', 2);
    $response->assertJsonPath('data.1.attributes.id', 4);
    $response->assertJsonPath('data.2.attributes.id', 1);
    $response->assertJsonPath('data.4.attributes.id', 3);
});

it('can sort incident updates by created date', function () {
    Incident::factory(5)->create();
    $incident = Incident::factory()->create();
    Update::factory()->count(4)->sequence(
        ['created_at' => '2022-01-01'],
        ['created_at' => '2020-01-01'],
        ['created_at' => '2023-01-01'],
        ['created_at' => '2021-01-01'],
    )->create([
        'updateable_type' => 'incident',
        'updateable_id' => $incident->id,
    ]);

    $response = getJson("/status/api/incidents/{$incident->id}/updates?sort=created_at");

    $response->assertJsonPath('data.0.attributes.id', 2);
    $response->assertJsonPath('data.1.attributes.id', 4);
    $response->assertJsonPath('data.2.attributes.id', 1);
    $response->assertJsonPath('data.3.attributes.id', 3);
});

it('can filter incident updates by status', function () {
    $incident = Incident::factory()->hasUpdates()->create([
        'status' => IncidentStatusEnum::investigating,
    ]);

    $incidentUpdate = Update::factory()->create([
        'updateable_type' => Relation::getMorphAlias(Incident::class),
        'updateable_id' => $incident->id,
        'status' => IncidentStatusEnum::watching,
    ]);

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
    Update::factory(5)->forIncident()->create();
    $incidentUpdate = Update::factory()->forIncident()->create();

    $response = getJson("/status/api/incidents/{$incidentUpdate->updateable_id}/updates/{$incidentUpdate->id}");

    $response->assertOk();
    $response->assertJsonFragment([
        'id' => $incidentUpdate->id,
    ]);
});

it('can get an incident update with incident', function () {
    Update::factory(5)->forIncident()->create();
    $incidentUpdate = Update::factory()->forIncident()->create();

    $response = getJson("/status/api/incidents/{$incidentUpdate->updateable_id}/updates/{$incidentUpdate->id}?include=incident");

    $response->assertOk();
    $response->assertJsonFragment([
        'id' => $incidentUpdate->id,
    ]);
});

it('cannot create an incident update if not authenticated', function () {
    $incident = Incident::factory()->create();

    $response = postJson("/status/api/incidents/{$incident->id}/updates", [
        'status' => IncidentStatusEnum::identified->value,
        'message' => 'This is a test message.',
    ]);

    $response->assertUnauthorized();
});

it('cannot create an incident update without the token ability', function () {
    Sanctum::actingAs(User::factory()->create());

    $incident = Incident::factory()->create();

    $response = postJson("/status/api/incidents/{$incident->id}/updates", [
        'status' => IncidentStatusEnum::identified->value,
        'message' => 'This is a test message.',
    ]);

    $response->assertForbidden();
});

it('can create an incident update', function () {
    Sanctum::actingAs(User::factory()->create(), ['incident-updates.manage']);

    $incident = Incident::factory()->create();

    $response = postJson("/status/api/incidents/{$incident->id}/updates", [
        'status' => IncidentStatusEnum::identified->value,
        'message' => 'This is a test message.',
    ]);

    $response->assertCreated();
    $this->assertDatabaseHas('updates', [
        'status' => IncidentStatusEnum::identified->value,
        'message' => 'This is a test message.',
    ]);
});

it('cannot update an incident update if not authenticated', function () {
    $incidentUpdate = Update::factory()->forIncident()->create();

    $response = putJson("/status/api/incidents/{$incidentUpdate->updateable_id}/updates/{$incidentUpdate->id}", [
        'message' => 'This is an updated message.',
    ]);

    $response->assertUnauthorized();
});

it('cannot update an incident update without the token ability', function () {
    Sanctum::actingAs(User::factory()->create());

    $incidentUpdate = Update::factory()->forIncident()->create();

    $response = putJson("/status/api/incidents/{$incidentUpdate->updateable_id}/updates/{$incidentUpdate->id}", [
        'message' => 'This is an updated message.',
    ]);

    $response->assertForbidden();
});

it('can update an incident update', function () {
    Sanctum::actingAs(User::factory()->create(), ['incident-updates.manage']);

    $incidentUpdate = Update::factory()->forIncident()->create();

    $data = [
        'message' => 'This is an updated message.',
    ];

    $response = putJson("/status/api/incidents/{$incidentUpdate->updateable_id}/updates/{$incidentUpdate->id}", $data);

    $response->assertOk();
    $this->assertDatabaseHas('updates', [
        'id' => $incidentUpdate->id,
        'status' => $incidentUpdate->status,
        ...$data,
    ]);
});

it('cannot delete an incident update if not authenticated', function () {
    $incidentUpdate = Update::factory()->forIncident()->create();

    $response = deleteJson("/status/api/incidents/{$incidentUpdate->updateable_id}/updates/{$incidentUpdate->id}");

    $response->assertUnauthorized();
});

it('cannot delete an incident update without the token ability', function () {
    Sanctum::actingAs(User::factory()->create());

    $incidentUpdate = Update::factory()->forIncident()->create();

    $response = deleteJson("/status/api/incidents/{$incidentUpdate->updateable_id}/updates/{$incidentUpdate->id}");

    $response->assertForbidden();
});

it('can delete an incident update', function () {
    Sanctum::actingAs(User::factory()->create(), ['incident-updates.delete']);

    $incidentUpdate = Update::factory()->forIncident()->create();

    $response = deleteJson("/status/api/incidents/{$incidentUpdate->updateable_id}/updates/{$incidentUpdate->id}");
    $response->assertNoContent();
    $this->assertDatabaseMissing('updates', [
        'updateable_type' => Relation::getMorphAlias(Incident::class),
        'updateable_id' => $incidentUpdate->updateable_id,
    ]);
});

it('cannot delete an incident update from another incident', function () {
    Sanctum::actingAs(User::factory()->create(), ['incidents.delete']);

    $incidentUpdate = Update::factory()->forUpdateable()->create();

    $response = deleteJson("/status/api/incidents/{$incidentUpdate->updateable_id}/updates/{$incidentUpdate->id}");
    $response->assertNotFound();
    $this->assertDatabaseHas('updates', [
        'updateable_type' => Relation::getMorphAlias(Incident::class),
        'updateable_id' => $incidentUpdate->updateable_id,
    ]);
});
