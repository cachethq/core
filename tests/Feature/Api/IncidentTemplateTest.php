<?php

use Cachet\Models\IncidentTemplate;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

it('can list incident templates', function () {
    IncidentTemplate::factory(2)->create();

    $response = getJson('/status/api/incident-templates');

    $response->assertOk();
    $response->assertJsonCount(2, 'data');
});

it('does not list more than 15 incident templates by default', function () {
    IncidentTemplate::factory(20)->create();

    $response = getJson('/status/api/incident-templates');

    $response->assertOk();
    $response->assertJsonCount(15, 'data');
});

it('can list more than 15 incident templates', function () {
    IncidentTemplate::factory(20)->create();

    $response = getJson('/status/api/incident-templates?per_page=18');

    $response->assertOk();
    $response->assertJsonCount(18, 'data');
});

it('can get an incident template', function () {
    $incidentTemplate = IncidentTemplate::factory()->create();

    $response = getJson('/status/api/incident-templates/'.$incidentTemplate->id);

    $response->assertOk();
    $response->assertJsonFragment([
        'id' => $incidentTemplate->id,
    ]);
});

it('can create an incident template', function () {
    $response = postJson('/status/api/incident-templates', [
        'name' => 'New Template',
        'slug' => 'new-template',
        'template' => 'Hello {{ name }}',
        'engine' => 'twig',
    ]);

    $response->assertCreated();
    $response->assertJsonFragment([
        'name' => 'New Template',
        'slug' => 'new-template',
        'template' => 'Hello {{ name }}',
        'engine' => 'twig',
    ]);
});

it('can update an incident template', function () {
    $incidentTemplate = IncidentTemplate::factory()->create();

    $response = putJson('/status/api/incident-templates/'.$incidentTemplate->id, [
        'name' => 'Updated Template',
    ]);

    $response->assertOk();
    $response->assertJsonFragment([
        'name' => 'Updated Template',
    ]);
});

it('can delete an incident template', function () {
    $incidentTemplate = IncidentTemplate::factory()->create();

    $response = deleteJson('/status/api/incident-templates/'.$incidentTemplate->id);

    $response->assertNoContent();
    $this->assertDatabaseMissing('incident_templates', [
        'id' => $incidentTemplate->id,
    ]);
});
