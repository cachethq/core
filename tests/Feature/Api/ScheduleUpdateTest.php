<?php

use Cachet\Models\Schedule;
use Cachet\Models\Update;
use Illuminate\Database\Eloquent\Relations\Relation;
use Laravel\Sanctum\Sanctum;
use Workbench\App\User;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

it('can list schedule updates', function () {
    $schedule = Schedule::factory()->hasUpdates(2)->create();

    $response = getJson("/status/api/schedules/{$schedule->id}/updates");

    $response->assertOk();
    $response->assertJsonCount(2, 'data');
});

it('does not list more than 15 schedule updates by default', function () {
    $schedule = Schedule::factory()->hasUpdates(20)->create();

    $response = getJson("/status/api/schedules/{$schedule->id}/updates");

    $response->assertOk();
    $response->assertJsonCount(15, 'data');
});

it('can list more than 15 schedule updates', function () {
    $schedule = Schedule::factory()->hasUpdates(20)->create();

    $response = getJson("/status/api/schedules/{$schedule->id}/updates?per_page=18");

    $response->assertOk();
    $response->assertJsonCount(18, 'data');
});

it('sorts schedule updates by id by default', function () {
    $schedule = Schedule::factory()->hasUpdates(20)->create();

    $response = getJson("/status/api/schedules/{$schedule->id}/updates");

    $response->assertJsonPath('data.0.attributes.id', 1);
    $response->assertJsonPath('data.1.attributes.id', 2);
    $response->assertJsonPath('data.2.attributes.id', 3);
    $response->assertJsonPath('data.3.attributes.id', 4);
    $response->assertJsonPath('data.4.attributes.id', 5);
});

it('can sort schedule updates by created date', function () {
    $schedule = Schedule::factory()->create();
    Update::factory()->count(4)->sequence(
        ['created_at' => '2022-01-01'],
        ['created_at' => '2020-01-01'],
        ['created_at' => '2023-01-01'],
        ['created_at' => '2021-01-01'],
    )->create([
        'updateable_type' => 'schedule',
        'updateable_id' => $schedule->id,
    ]);

    $response = getJson("/status/api/schedules/{$schedule->id}/updates?sort=created_at");

    $response->assertJsonPath('data.0.attributes.id', 2);
    $response->assertJsonPath('data.1.attributes.id', 4);
    $response->assertJsonPath('data.2.attributes.id', 1);
    $response->assertJsonPath('data.3.attributes.id', 3);
});

it('can get an schedule update', function () {
    $scheduleUpdate = Update::factory()->forSchedule()->create();

    $response = getJson("/status/api/schedules/{$scheduleUpdate->updateable_id}/updates/{$scheduleUpdate->id}");

    $response->assertOk();
    $response->assertJsonFragment([
        'id' => $scheduleUpdate->id,
    ]);
});

it('can get an schedule update with schedule', function () {
    $scheduleUpdate = Update::factory()->forSchedule()->create();

    $response = getJson("/status/api/schedules/{$scheduleUpdate->updateable_id}/updates/{$scheduleUpdate->id}?include=schedule");

    $response->assertOk();
    $response->assertJsonFragment([
        'id' => $scheduleUpdate->id,
    ]);
});

it('cannot create a schedule if not authenticated', function () {
    $schedule = Schedule::factory()->create();

    $response = postJson("/status/api/schedules/{$schedule->id}/updates", [
        'message' => 'This is a message.',
    ]);

    $response->assertUnauthorized();
});

it('cannot create a schedule without the token ability', function () {
    Sanctum::actingAs(User::factory()->create());

    $schedule = Schedule::factory()->create();

    $response = postJson("/status/api/schedules/{$schedule->id}/updates", [
        'message' => 'This is a message.',
    ]);

    $response->assertForbidden();
});

it('can create a schedule update', function () {
    Sanctum::actingAs(User::factory()->create(), ['schedule-updates.manage']);

    $schedule = Schedule::factory()->create();

    $response = postJson("/status/api/schedules/{$schedule->id}/updates", [
        'message' => 'This is a message.',
    ]);

    $response->assertCreated();
    $this->assertDatabaseHas('updates', [
        'message' => 'This is a message.',
    ]);
});

it('cannot update a schedule if not authenticated', function () {
    $scheduleUpdate = Update::factory()->forSchedule()->create();

    $data = [
        'message' => 'This is an updated message.',
    ];

    $response = putJson("/status/api/schedules/{$scheduleUpdate->updateable_id}/updates/{$scheduleUpdate->id}", $data);

    $response->assertUnauthorized();
});

it('cannot update a schedule without the token ability', function () {
    Sanctum::actingAs(User::factory()->create());

    $scheduleUpdate = Update::factory()->forSchedule()->create();

    $data = [
        'message' => 'This is an updated message.',
    ];

    $response = putJson("/status/api/schedules/{$scheduleUpdate->updateable_id}/updates/{$scheduleUpdate->id}", $data);

    $response->assertForbidden();
});

it('can update an schedule update', function () {
    Sanctum::actingAs(User::factory()->create(), ['schedule-updates.manage']);

    $scheduleUpdate = Update::factory()->forSchedule()->create();

    $data = [
        'message' => 'This is an updated message.',
    ];

    $response = putJson("/status/api/schedules/{$scheduleUpdate->updateable_id}/updates/{$scheduleUpdate->id}", $data);

    $response->assertOk();
    $this->assertDatabaseHas('updates', [
        'id' => $scheduleUpdate->id,
        'status' => $scheduleUpdate->status,
        ...$data,
    ]);
});

it('cannot delete a schedule if not authenticated', function () {
    $scheduleUpdate = Update::factory()->forSchedule()->create();

    $response = deleteJson("/status/api/schedules/{$scheduleUpdate->updateable_id}/updates/{$scheduleUpdate->id}");

    $response->assertUnauthorized();
});

it('cannot delete a schedule without the token ability', function () {
    Sanctum::actingAs(User::factory()->create());

    $scheduleUpdate = Update::factory()->forSchedule()->create();

    $response = deleteJson("/status/api/schedules/{$scheduleUpdate->updateable_id}/updates/{$scheduleUpdate->id}");

    $response->assertForbidden();
});

it('can delete an schedule update', function () {
    Sanctum::actingAs(User::factory()->create(), ['schedule-updates.delete']);

    $scheduleUpdate = Update::factory()->forSchedule()->create();

    $response = deleteJson("/status/api/schedules/{$scheduleUpdate->updateable_id}/updates/{$scheduleUpdate->id}");
    $response->assertNoContent();
    $this->assertDatabaseMissing('updates', [
        'updateable_type' => Relation::getMorphAlias(Schedule::class),
        'updateable_id' => $scheduleUpdate->updateable_id,
    ]);
});

it('cannot delete an schedule update from another schedule', function () {
    Sanctum::actingAs(User::factory()->create(), ['schedule-updates.delete']);

    $schedule = Schedule::factory()->create();
    $scheduleUpdate = Update::factory()->forSchedule()->create();

    $response = deleteJson("/status/api/schedules/{$schedule->id}/updates/{$scheduleUpdate->id}");

    $response->assertNotFound();
    $this->assertDatabaseHas('updates', [
        'updateable_type' => Relation::getMorphAlias(Schedule::class),
        'updateable_id' => $scheduleUpdate->updateable_id,
    ]);
});
