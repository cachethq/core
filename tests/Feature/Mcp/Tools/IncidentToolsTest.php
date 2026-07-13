<?php

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Mcp\CachetServer;
use Cachet\Mcp\Tools\Incidents\CreateIncident;
use Cachet\Mcp\Tools\Incidents\DeleteIncident;
use Cachet\Mcp\Tools\Incidents\GetIncident;
use Cachet\Mcp\Tools\Incidents\ListIncidents;
use Cachet\Mcp\Tools\Incidents\UpdateIncident;
use Cachet\Mcp\Tools\IncidentTemplates\CreateIncidentTemplate;
use Cachet\Mcp\Tools\IncidentTemplates\DeleteIncidentTemplate;
use Cachet\Mcp\Tools\IncidentTemplates\ListIncidentTemplates;
use Cachet\Mcp\Tools\IncidentTemplates\UpdateIncidentTemplate;
use Cachet\Mcp\Tools\IncidentUpdates\DeleteIncidentUpdate;
use Cachet\Mcp\Tools\IncidentUpdates\EditIncidentUpdate;
use Cachet\Mcp\Tools\IncidentUpdates\RecordIncidentUpdate;
use Cachet\Models\Component;
use Cachet\Models\Incident;
use Cachet\Models\IncidentTemplate;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Workbench\App\User;

use function Pest\Laravel\assertDatabaseHas;

it('lists incidents for guests', function () {
    Incident::factory(3)->create();

    CachetServer::tool(ListIncidents::class)
        ->assertOk()
        ->assertStructuredContent(fn (AssertableJson $json) => $json->has('data', 3)->etc());
});

it('filters incidents by status', function () {
    Incident::factory()->create(['status' => IncidentStatusEnum::investigating]);
    Incident::factory()->create(['status' => IncidentStatusEnum::fixed]);

    CachetServer::tool(ListIncidents::class, ['status' => IncidentStatusEnum::fixed->value])
        ->assertOk()
        ->assertStructuredContent(fn (AssertableJson $json) => $json->has('data', 1)->etc());
});

it('gets an incident with its updates and components', function () {
    $incident = Incident::factory()->create(['name' => 'Database Outage']);
    $incident->updates()->create(['status' => IncidentStatusEnum::watching, 'message' => 'Monitoring the fix.']);

    CachetServer::tool(GetIncident::class, ['id' => $incident->id])
        ->assertOk()
        ->assertSee('Database Outage')
        ->assertSee('Monitoring the fix.');
});

it('returns an error for an unknown incident', function () {
    CachetServer::tool(GetIncident::class, ['id' => 999])
        ->assertHasErrors(['Incident [999] not found.']);
});

it('hides incident write tools from guests', function () {
    CachetServer::tool(CreateIncident::class, [
        'name' => 'Incident',
        'status' => IncidentStatusEnum::investigating->value,
        'message' => 'Something broke.',
    ])->assertHasErrors();
});

it('creates an incident', function () {
    Sanctum::actingAs(User::factory()->create(), ['incidents.manage']);

    CachetServer::tool(CreateIncident::class, [
        'name' => 'API Slowdown',
        'status' => IncidentStatusEnum::investigating->value,
        'message' => 'We are investigating API latency.',
    ])->assertOk()->assertSee('API Slowdown');

    assertDatabaseHas('incidents', ['name' => 'API Slowdown']);
});

it('creates an incident and updates the affected components', function () {
    Sanctum::actingAs(User::factory()->create(), ['incidents.manage']);

    $component = Component::factory()->create(['status' => ComponentStatusEnum::operational]);

    CachetServer::tool(CreateIncident::class, [
        'name' => 'API Outage',
        'status' => IncidentStatusEnum::identified->value,
        'message' => 'The API is down.',
        'components' => [
            ['id' => $component->id, 'status' => ComponentStatusEnum::major_outage->value],
        ],
    ])->assertOk();

    $incident = Incident::query()->firstWhere('name', 'API Outage');

    expect($incident->incidentComponents()->first())
        ->component_id->toBe($component->id)
        ->component_status->toBe(ComponentStatusEnum::major_outage);
});

it('creates an incident from a template', function () {
    Sanctum::actingAs(User::factory()->create(), ['incidents.manage']);

    IncidentTemplate::factory()->create([
        'slug' => 'server-fire',
        'template' => 'The server is on fire.',
    ]);

    CachetServer::tool(CreateIncident::class, [
        'name' => 'Fire',
        'status' => IncidentStatusEnum::investigating->value,
        'template' => 'server-fire',
    ])->assertOk()->assertSee('The server is on fire.');
});

it('requires a message or template when creating an incident', function () {
    Sanctum::actingAs(User::factory()->create(), ['incidents.manage']);

    CachetServer::tool(CreateIncident::class, [
        'name' => 'Incident',
        'status' => IncidentStatusEnum::investigating->value,
    ])->assertHasErrors();
});

it('updates an incident', function () {
    Sanctum::actingAs(User::factory()->create(), ['incidents.manage']);

    $incident = Incident::factory()->create(['stickied' => false]);

    CachetServer::tool(UpdateIncident::class, [
        'id' => $incident->id,
        'stickied' => true,
    ])->assertOk();

    expect($incident->fresh()->stickied)->toBeTrue();
});

it('deletes an incident', function () {
    Sanctum::actingAs(User::factory()->create(), ['incidents.delete']);

    $incident = Incident::factory()->create();

    CachetServer::tool(DeleteIncident::class, ['id' => $incident->id])
        ->assertOk();

    expect(Incident::query()->find($incident->id))->toBeNull();
});

it('records an incident update', function () {
    Sanctum::actingAs(User::factory()->create(), ['incident-updates.manage']);

    $incident = Incident::factory()->create(['status' => IncidentStatusEnum::investigating]);

    CachetServer::tool(RecordIncidentUpdate::class, [
        'incident_id' => $incident->id,
        'status' => IncidentStatusEnum::identified->value,
        'message' => 'We found the problem.',
    ])->assertOk()->assertSee('We found the problem.');

    expect($incident->updates()->count())->toBe(1);
});

it('resolves the incident and its components when recording a fixed update', function () {
    Sanctum::actingAs(User::factory()->create(), ['incident-updates.manage']);

    $component = Component::factory()->create(['status' => ComponentStatusEnum::major_outage]);
    $incident = Incident::factory()->create(['status' => IncidentStatusEnum::identified]);
    $incident->components()->attach($component->id, ['component_status' => ComponentStatusEnum::major_outage]);

    CachetServer::tool(RecordIncidentUpdate::class, [
        'incident_id' => $incident->id,
        'status' => IncidentStatusEnum::fixed->value,
        'message' => 'The problem is resolved.',
    ])->assertOk();

    expect($incident->fresh()->status)->toBe(IncidentStatusEnum::fixed)
        ->and($incident->incidentComponents()->first()->component_status)->toBe(ComponentStatusEnum::operational);
});

it('edits an incident update', function () {
    Sanctum::actingAs(User::factory()->create(), ['incident-updates.manage']);

    $incident = Incident::factory()->create();
    $update = $incident->updates()->create(['status' => IncidentStatusEnum::watching, 'message' => 'Old message.']);

    CachetServer::tool(EditIncidentUpdate::class, [
        'incident_id' => $incident->id,
        'update_id' => $update->id,
        'message' => 'New message.',
    ])->assertOk();

    expect($update->fresh()->message)->toBe('New message.');
});

it('deletes an incident update', function () {
    Sanctum::actingAs(User::factory()->create(), ['incident-updates.delete']);

    $incident = Incident::factory()->create();
    $update = $incident->updates()->create(['status' => IncidentStatusEnum::watching, 'message' => 'Message.']);

    CachetServer::tool(DeleteIncidentUpdate::class, [
        'incident_id' => $incident->id,
        'update_id' => $update->id,
    ])->assertOk();

    expect($incident->updates()->count())->toBe(0);
});

it('scopes incident updates to the incident', function () {
    Sanctum::actingAs(User::factory()->create(), ['incident-updates.manage']);

    $incident = Incident::factory()->create();
    $other = Incident::factory()->create();
    $update = $other->updates()->create(['status' => IncidentStatusEnum::watching, 'message' => 'Message.']);

    CachetServer::tool(EditIncidentUpdate::class, [
        'incident_id' => $incident->id,
        'update_id' => $update->id,
        'message' => 'Hijacked.',
    ])->assertHasErrors(["Update [{$update->id}] not found on incident [{$incident->id}]."]);
});

it('lists incident templates', function () {
    IncidentTemplate::factory()->create(['name' => 'Standard Outage']);

    CachetServer::tool(ListIncidentTemplates::class)
        ->assertOk()
        ->assertSee('Standard Outage');
});

it('creates an incident template', function () {
    Sanctum::actingAs(User::factory()->create(), ['incident-templates.manage']);

    CachetServer::tool(CreateIncidentTemplate::class, [
        'name' => 'Third Party Outage',
        'template' => 'A third party provider is having issues.',
    ])->assertOk();

    assertDatabaseHas('incident_templates', ['slug' => 'third-party-outage']);
});

it('updates an incident template', function () {
    Sanctum::actingAs(User::factory()->create(), ['incident-templates.manage']);

    $template = IncidentTemplate::factory()->create();

    CachetServer::tool(UpdateIncidentTemplate::class, [
        'id' => $template->id,
        'template' => 'Updated body.',
    ])->assertOk();

    expect($template->fresh()->template)->toBe('Updated body.');
});

it('deletes an incident template', function () {
    Sanctum::actingAs(User::factory()->create(), ['incident-templates.delete']);

    $template = IncidentTemplate::factory()->create();

    CachetServer::tool(DeleteIncidentTemplate::class, ['id' => $template->id])
        ->assertOk();

    expect(IncidentTemplate::query()->find($template->id))->toBeNull();
});
