<?php

use Cachet\Mcp\CachetServer;
use Cachet\Mcp\Tools\Incidents\GetIncident;
use Cachet\Mcp\Tools\Incidents\ListIncidents;
use Cachet\Models\Incident;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Workbench\App\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;

it('hides incidents scheduled to publish in the future from the api', function () {
    Incident::factory()->create(['name' => 'Published now']);
    Incident::factory()->scheduled()->create(['name' => 'Prescheduled']);

    $response = getJson('/status/api/incidents');

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.attributes.name', 'Published now');
});

it('includes incidents whose publish date has passed', function () {
    Incident::factory()->published()->create(['name' => 'Already live']);

    $response = getJson('/status/api/incidents');

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
});

it('returns 404 from the api for a single unpublished incident', function () {
    $incident = Incident::factory()->scheduled()->create();

    getJson("/status/api/incidents/{$incident->id}")->assertNotFound();
});

it('hides unpublished incidents from a read-only api token', function () {
    Incident::factory()->scheduled()->create();

    Sanctum::actingAs(User::factory()->create(), ['incidents.view']);

    getJson('/status/api/incidents')
        ->assertOk()
        ->assertJsonCount(0, 'data');
});

it('exposes unpublished incidents to a first-party session on the api', function () {
    Incident::factory()->scheduled()->create();

    // First-party sessions authenticate with a transient token that grants every
    // ability, so a logged-in staff user is treated as a manager here.
    actingAs(User::factory()->create())
        ->getJson('/status/api/incidents')
        ->assertOk()
        ->assertJsonCount(1, 'data');
});

it('exposes unpublished incidents to an api token that can manage incidents', function () {
    Incident::factory()->create(['name' => 'Published now']);
    Incident::factory()->scheduled()->create(['name' => 'Prescheduled']);

    Sanctum::actingAs(User::factory()->create(), ['incidents.manage']);

    getJson('/status/api/incidents')
        ->assertOk()
        ->assertJsonCount(2, 'data');
});

it('exposes a single unpublished incident to an api token that can manage incidents', function () {
    $incident = Incident::factory()->scheduled()->create();

    Sanctum::actingAs(User::factory()->create(), ['incidents.manage']);

    getJson("/status/api/incidents/{$incident->id}")->assertOk();
});

it('hides an unpublished incident page from guests', function () {
    $incident = Incident::factory()->scheduled()->create();

    $this->get(route('cachet.status-page.incident', $incident))->assertNotFound();
});

it('lets authenticated dashboard users preview an unpublished incident page', function () {
    $incident = Incident::factory()->scheduled()->create();

    actingAs(User::factory()->create())
        ->get(route('cachet.status-page.incident', $incident))
        ->assertOk();
});

it('keeps unpublished incidents out of the timeline', function () {
    Incident::factory()->create(['name' => 'Visible incident', 'occurred_at' => now()->subDay()]);
    Incident::factory()->scheduled()->create(['name' => 'Hidden incident', 'occurred_at' => now()->subDay()]);

    $this->get(route('cachet.status-page'))
        ->assertOk()
        ->assertSee('Visible incident')
        ->assertDontSee('Hidden incident');
});

it('exposes unpublished incidents to the mcp server for dashboard management', function () {
    Incident::factory()->create(['name' => 'Published now']);
    Incident::factory()->scheduled()->create(['name' => 'Prescheduled']);

    CachetServer::tool(ListIncidents::class)
        ->assertOk()
        ->assertStructuredContent(fn (AssertableJson $json) => $json->has('data', 2)->etc());
});

it('exposes a single unpublished incident to the mcp server', function () {
    $incident = Incident::factory()->scheduled()->create(['name' => 'Prescheduled']);

    CachetServer::tool(GetIncident::class, ['id' => $incident->id])
        ->assertOk()
        ->assertSee('Prescheduled');
});
