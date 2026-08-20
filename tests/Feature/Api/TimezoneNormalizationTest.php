<?php

use Cachet\Models\Incident;
use Cachet\Models\Metric;
use Cachet\Models\Schedule;
use Laravel\Sanctum\Sanctum;
use Workbench\App\User;

use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

afterEach(function () {
    config()->set('app.timezone', 'UTC');
    date_default_timezone_set('UTC');
});

function useAppTimezone(string $timezone): void
{
    config()->set('app.timezone', $timezone);
    date_default_timezone_set($timezone);
}

it('normalizes incident occurred_at with a UTC designator to the app timezone', function () {
    useAppTimezone('Europe/Berlin');

    Sanctum::actingAs(User::factory()->create(), ['incidents.manage']);

    $response = postJson('/status/api/incidents', [
        'name' => 'Incident',
        'message' => 'Something went wrong.',
        'status' => 1,
        'occurred_at' => '2026-08-06T18:31:37Z',
    ]);

    $response->assertCreated();
    $response->assertJsonPath('data.attributes.occurred.string', '2026-08-06 20:31:37');
    $this->assertDatabaseHas('incidents', ['occurred_at' => '2026-08-06 20:31:37']);
});

it('normalizes incident occurred_at with an explicit offset to the app timezone', function () {
    useAppTimezone('Europe/Berlin');

    Sanctum::actingAs(User::factory()->create(), ['incidents.manage']);

    $response = postJson('/status/api/incidents', [
        'name' => 'Incident',
        'message' => 'Something went wrong.',
        'status' => 1,
        'occurred_at' => '2026-08-06T18:31:37+05:30',
    ]);

    $response->assertCreated();
    $response->assertJsonPath('data.attributes.occurred.string', '2026-08-06 15:01:37');
    $this->assertDatabaseHas('incidents', ['occurred_at' => '2026-08-06 15:01:37']);
});

it('interprets offset-less incident occurred_at as app timezone wall clock', function () {
    useAppTimezone('Europe/Berlin');

    Sanctum::actingAs(User::factory()->create(), ['incidents.manage']);

    $response = postJson('/status/api/incidents', [
        'name' => 'Incident',
        'message' => 'Something went wrong.',
        'status' => 1,
        'occurred_at' => '2026-08-06 18:31:37',
    ]);

    $response->assertCreated();
    $response->assertJsonPath('data.attributes.occurred.string', '2026-08-06 18:31:37');
    $this->assertDatabaseHas('incidents', ['occurred_at' => '2026-08-06 18:31:37']);
});

it('normalizes incident published_at with an explicit offset to the app timezone', function () {
    useAppTimezone('Europe/Berlin');

    Sanctum::actingAs(User::factory()->create(), ['incidents.manage']);

    $response = postJson('/status/api/incidents', [
        'name' => 'Incident',
        'message' => 'Something went wrong.',
        'status' => 1,
        'published_at' => '2026-08-06T18:31:37+05:30',
    ]);

    $response->assertCreated();
    $this->assertDatabaseHas('incidents', ['published_at' => '2026-08-06 15:01:37']);
});

it('normalizes occurred_at on incident update', function () {
    useAppTimezone('Europe/Berlin');

    Sanctum::actingAs(User::factory()->create(), ['incidents.manage']);

    $incident = Incident::factory()->create();

    $response = putJson('/status/api/incidents/'.$incident->id, [
        'occurred_at' => '2026-08-06T18:31:37+05:30',
    ]);

    $response->assertOk();
    $this->assertDatabaseHas('incidents', [
        'id' => $incident->id,
        'occurred_at' => '2026-08-06 15:01:37',
    ]);
});

it('normalizes schedule scheduled_at and completed_at with explicit offsets to the app timezone', function () {
    useAppTimezone('Europe/Berlin');

    Sanctum::actingAs(User::factory()->create(), ['schedules.manage']);

    $response = postJson('/status/api/schedules', [
        'name' => 'Maintenance',
        'message' => 'Something will go wrong.',
        'scheduled_at' => '2026-08-06T18:31:37Z',
        'completed_at' => '2026-08-06T22:31:37+05:30',
    ]);

    $response->assertCreated();
    $response->assertJsonPath('data.attributes.scheduled.string', '2026-08-06 20:31:37');
    $response->assertJsonPath('data.attributes.completed.string', '2026-08-06 19:01:37');
    $this->assertDatabaseHas('schedules', [
        'scheduled_at' => '2026-08-06 20:31:37',
        'completed_at' => '2026-08-06 19:01:37',
    ]);
});

it('normalizes completed_at with an explicit offset on schedule update', function () {
    useAppTimezone('Europe/Berlin');

    Sanctum::actingAs(User::factory()->create(), ['schedules.manage']);

    $schedule = Schedule::factory()->create();

    $response = putJson('/status/api/schedules/'.$schedule->id, [
        'completed_at' => '2026-08-06T18:31:37+05:30',
    ]);

    $response->assertOk();
    $this->assertDatabaseHas('schedules', [
        'id' => $schedule->id,
        'completed_at' => '2026-08-06 15:01:37',
    ]);
});

it('normalizes an ISO 8601 metric point timestamp with an explicit offset to the app timezone', function () {
    useAppTimezone('Europe/Berlin');

    Sanctum::actingAs(User::factory()->create(), ['metric-points.manage']);

    $metric = Metric::factory()->create();

    $response = postJson('/status/api/metrics/'.$metric->id.'/points', [
        'value' => 1,
        'timestamp' => '2026-08-06T18:31:30+05:30',
    ]);

    $response->assertCreated();
    $this->assertDatabaseHas('metric_points', [
        'metric_id' => $metric->id,
        'created_at' => '2026-08-06 15:01:30',
    ]);
});

it('normalizes incident occurred_at with an explicit offset when the app timezone is UTC', function () {
    Sanctum::actingAs(User::factory()->create(), ['incidents.manage']);

    $response = postJson('/status/api/incidents', [
        'name' => 'Incident',
        'message' => 'Something went wrong.',
        'status' => 1,
        'occurred_at' => '2026-08-06T18:31:37+05:30',
    ]);

    $response->assertCreated();
    $response->assertJsonPath('data.attributes.occurred.string', '2026-08-06 13:01:37');
    $this->assertDatabaseHas('incidents', ['occurred_at' => '2026-08-06 13:01:37']);
});
