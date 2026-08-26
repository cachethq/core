<?php

use Cachet\Mcp\CachetServer;
use Cachet\Mcp\Tools\Schedules\GetSchedule;
use Cachet\Mcp\Tools\Schedules\ListSchedules;
use Cachet\Models\Schedule;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Workbench\App\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;

it('hides schedules scheduled to publish in the future from the api', function () {
    Schedule::factory()->create(['name' => 'Published now']);
    Schedule::factory()->scheduled()->create(['name' => 'Prescheduled']);

    $response = getJson('/status/api/schedules');

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.attributes.name', 'Published now');
});

it('returns 404 from the api for a single unpublished schedule', function () {
    $schedule = Schedule::factory()->scheduled()->create();

    getJson("/status/api/schedules/{$schedule->id}")->assertNotFound();
});

it('hides unpublished schedules from a read-only api token', function () {
    Schedule::factory()->scheduled()->create();

    Sanctum::actingAs(User::factory()->create(), ['schedules.view']);

    getJson('/status/api/schedules')
        ->assertOk()
        ->assertJsonCount(0, 'data');
});

it('exposes unpublished schedules to an api token that can manage schedules', function () {
    Schedule::factory()->create(['name' => 'Published now']);
    Schedule::factory()->scheduled()->create(['name' => 'Prescheduled']);

    Sanctum::actingAs(User::factory()->create(), ['schedules.manage']);

    getJson('/status/api/schedules')
        ->assertOk()
        ->assertJsonCount(2, 'data');
});

it('exposes a single unpublished schedule to an api token that can manage schedules', function () {
    $schedule = Schedule::factory()->scheduled()->create();

    Sanctum::actingAs(User::factory()->create(), ['schedules.manage']);

    getJson("/status/api/schedules/{$schedule->id}")->assertOk();
});

it('hides an unpublished schedule from the status page maintenance block', function () {
    Schedule::factory()->create([
        'name' => 'Visible maintenance',
        'scheduled_at' => now()->addDay(),
        'completed_at' => now()->addDays(2),
    ]);
    Schedule::factory()->scheduled()->create([
        'name' => 'Hidden maintenance',
        'scheduled_at' => now()->addDay(),
        'completed_at' => now()->addDays(2),
    ]);

    $this->get(route('cachet.status-page'))
        ->assertOk()
        ->assertSee('Visible maintenance')
        ->assertDontSee('Hidden maintenance');
});

it('hides an unpublished schedule page from guests', function () {
    $schedule = Schedule::factory()->scheduled()->create();

    $this->get(route('cachet.status-page.schedule', $schedule))->assertNotFound();
});

it('lets authenticated dashboard users preview an unpublished schedule page', function () {
    $schedule = Schedule::factory()->scheduled()->create();

    actingAs(User::factory()->create())
        ->get(route('cachet.status-page.schedule', $schedule))
        ->assertOk();
});

it('exposes unpublished schedules to the mcp server for dashboard management', function () {
    Schedule::factory()->create(['name' => 'Published now']);
    Schedule::factory()->scheduled()->create(['name' => 'Prescheduled']);

    Sanctum::actingAs(User::factory()->create(), ['schedules.manage']);

    CachetServer::tool(ListSchedules::class)
        ->assertOk()
        ->assertStructuredContent(fn (AssertableJson $json) => $json->has('data', 2)->etc());
});

it('exposes a single unpublished schedule to the mcp server', function () {
    $schedule = Schedule::factory()->scheduled()->create(['name' => 'Prescheduled']);

    Sanctum::actingAs(User::factory()->create(), ['schedules.manage']);

    CachetServer::tool(GetSchedule::class, ['id' => $schedule->id])
        ->assertOk()
        ->assertSee('Prescheduled');
});
