<?php

use Cachet\Models\IncidentTemplate;
use Laravel\Sanctum\Sanctum;
use Workbench\App\User;

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
    IncidentTemplate::factory(5)->create();
    $incidentTemplate = IncidentTemplate::factory()->create();

    $response = getJson('/status/api/incident-templates/'.$incidentTemplate->id);

    $response->assertOk();
    $response->assertJsonFragment([
        'id' => $incidentTemplate->id,
    ]);
});

it('cannot create an incident template when not authenticated', function () {
    $response = postJson('/status/api/incident-templates', [
        'name' => 'New Template',
        'slug' => 'new-template',
        'template' => 'Hello {{ name }}',
        'engine' => 'twig',
    ]);

    $response->assertUnauthorized();
});

it('cannot create an incident template without the token ability', function () {
    Sanctum::actingAs(User::factory()->create());

    $response = postJson('/status/api/incident-templates', [
        'name' => 'New Template',
        'slug' => 'new-template',
        'template' => 'Hello {{ name }}',
        'engine' => 'twig',
    ]);

    $response->assertForbidden();
});

it('can create an incident template', function () {
    Sanctum::actingAs(User::factory()->create(), ['incident-templates.manage']);

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

it('cannot update an incident template when not authenticated', function () {
    $incidentTemplate = IncidentTemplate::factory()->create();

    $response = putJson('/status/api/incident-templates/'.$incidentTemplate->id, [
        'name' => 'Updated Template',
    ]);

    $response->assertUnauthorized();
});

it('cannot update an incident template without the token ability', function () {
    Sanctum::actingAs(User::factory()->create());

    $incidentTemplate = IncidentTemplate::factory()->create();

    $response = putJson('/status/api/incident-templates/'.$incidentTemplate->id, [
        'name' => 'Updated Template',
    ]);

    $response->assertForbidden();
});

it('can update an incident template', function () {
    Sanctum::actingAs(User::factory()->create(), ['incident-templates.manage']);

    $incidentTemplate = IncidentTemplate::factory()->create();

    $response = putJson('/status/api/incident-templates/'.$incidentTemplate->id, [
        'name' => 'Updated Template',
    ]);

    $response->assertOk();
    $response->assertJsonFragment([
        'name' => 'Updated Template',
    ]);
});

it('cannot delete an incident template when not authenticated', function () {
    $incidentTemplate = IncidentTemplate::factory()->create();

    $response = deleteJson('/status/api/incident-templates/'.$incidentTemplate->id);

    $response->assertUnauthorized();
});

it('cannot delete an incident template without the token ability', function () {
    Sanctum::actingAs(User::factory()->create());

    $incidentTemplate = IncidentTemplate::factory()->create();

    $response = deleteJson('/status/api/incident-templates/'.$incidentTemplate->id);

    $response->assertForbidden();
});

it('can delete an incident template', function () {
    Sanctum::actingAs(User::factory()->create(), ['incident-templates.delete']);

    $incidentTemplate = IncidentTemplate::factory()->create();

    $response = deleteJson('/status/api/incident-templates/'.$incidentTemplate->id);

    $response->assertNoContent();
    $this->assertDatabaseMissing('incident_templates', [
        'id' => $incidentTemplate->id,
    ]);
});
