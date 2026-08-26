<?php

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\ScheduleStatusEnum;
use Cachet\Mcp\CachetServer;
use Cachet\Mcp\Tools\Schedules\CreateSchedule;
use Cachet\Mcp\Tools\Schedules\DeleteSchedule;
use Cachet\Mcp\Tools\Schedules\GetSchedule;
use Cachet\Mcp\Tools\Schedules\ListSchedules;
use Cachet\Mcp\Tools\Schedules\UpdateSchedule;
use Cachet\Mcp\Tools\ScheduleUpdates\DeleteScheduleUpdate;
use Cachet\Mcp\Tools\ScheduleUpdates\EditScheduleUpdate;
use Cachet\Mcp\Tools\ScheduleUpdates\RecordScheduleUpdate;
use Cachet\Models\Component;
use Cachet\Models\Schedule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Workbench\App\User;

use function Pest\Laravel\assertDatabaseHas;

it('lists schedules for guests', function () {
    Schedule::factory(2)->create();

    CachetServer::tool(ListSchedules::class)
        ->assertOk()
        ->assertStructuredContent(fn (AssertableJson $json) => $json->has('data', 2)->etc());
});

it('gets a schedule by id', function () {
    $schedule = Schedule::factory()->create(['name' => 'Database Upgrade']);
    $schedule->syncTags(['Database']);
    $schedule->components()->attach(Component::factory(2)->create(), [
        'component_status' => ComponentStatusEnum::under_maintenance,
    ]);

    Model::preventLazyLoading();

    try {
        CachetServer::tool(GetSchedule::class, ['id' => $schedule->id])
            ->assertOk()
            ->assertSee('Database Upgrade');
    } finally {
        Model::preventLazyLoading(false);
    }
});

it('returns an error for an unknown schedule', function () {
    CachetServer::tool(GetSchedule::class, ['id' => 999])
        ->assertHasErrors(['Schedule [999] not found.']);
});

it('creates a schedule', function () {
    Sanctum::actingAs(User::factory()->create(), ['schedules.manage']);

    CachetServer::tool(CreateSchedule::class, [
        'name' => 'Server Maintenance',
        'message' => 'We will be upgrading the servers.',
        'scheduled_at' => now()->addDay()->format('Y-m-d H:i:s'),
    ])->assertOk()->assertSee('Server Maintenance');

    assertDatabaseHas('schedules', ['name' => 'Server Maintenance']);
});

it('validates schedule input', function () {
    Sanctum::actingAs(User::factory()->create(), ['schedules.manage']);

    CachetServer::tool(CreateSchedule::class, [
        'name' => 'Missing everything else',
    ])->assertHasErrors();
});

it('updates a schedule', function () {
    Sanctum::actingAs(User::factory()->create(), ['schedules.manage']);

    $schedule = Schedule::factory()->create(['name' => 'Old Name']);

    CachetServer::tool(UpdateSchedule::class, [
        'id' => $schedule->id,
        'name' => 'New Name',
        'tags' => ['Infrastructure'],
    ])->assertOk();

    expect($schedule->fresh()->name)->toBe('New Name');
    expect($schedule->fresh()->tags->pluck('name')->all())->toBe(['Infrastructure']);
});

it('deletes a schedule', function () {
    Sanctum::actingAs(User::factory()->create(), ['schedules.delete']);

    $schedule = Schedule::factory()->create();

    CachetServer::tool(DeleteSchedule::class, ['id' => $schedule->id])
        ->assertOk();

    expect(Schedule::query()->find($schedule->id))->toBeNull();
});

it('records a schedule update', function () {
    Sanctum::actingAs(User::factory()->create(), ['schedule-updates.manage']);

    $schedule = Schedule::factory()->create(['completed_at' => null]);

    CachetServer::tool(RecordScheduleUpdate::class, [
        'schedule_id' => $schedule->id,
        'message' => 'Maintenance is progressing well.',
    ])->assertOk()->assertSee('Maintenance is progressing well.');

    expect($schedule->updates()->count())->toBe(1);
});

it('completes the schedule when recording an update with completed at', function () {
    Sanctum::actingAs(User::factory()->create(), ['schedule-updates.manage']);

    $schedule = Schedule::factory()->create([
        'scheduled_at' => now()->subHour(),
        'completed_at' => null,
    ]);

    CachetServer::tool(RecordScheduleUpdate::class, [
        'schedule_id' => $schedule->id,
        'message' => 'Maintenance is complete.',
        'completed_at' => now()->format('Y-m-d H:i:s'),
    ])->assertOk();

    expect($schedule->fresh())
        ->completed_at->not->toBeNull()
        ->status->toBe(ScheduleStatusEnum::complete);
});

it('edits a schedule update', function () {
    Sanctum::actingAs(User::factory()->create(), ['schedule-updates.manage']);

    $schedule = Schedule::factory()->create();
    $update = $schedule->updates()->create(['message' => 'Old message.']);

    CachetServer::tool(EditScheduleUpdate::class, [
        'schedule_id' => $schedule->id,
        'update_id' => $update->id,
        'message' => 'New message.',
    ])->assertOk();

    expect($update->fresh()->message)->toBe('New message.');
});

it('deletes a schedule update', function () {
    Sanctum::actingAs(User::factory()->create(), ['schedule-updates.delete']);

    $schedule = Schedule::factory()->create();
    $update = $schedule->updates()->create(['message' => 'Message.']);

    CachetServer::tool(DeleteScheduleUpdate::class, [
        'schedule_id' => $schedule->id,
        'update_id' => $update->id,
    ])->assertOk();

    expect($schedule->updates()->count())->toBe(0);
});

it('accepts iso-8601 datetimes when creating a schedule', function () {
    Sanctum::actingAs(User::factory()->create(), ['schedules.manage']);

    CachetServer::tool(CreateSchedule::class, [
        'name' => 'Timezone Maintenance',
        'message' => 'Testing ISO-8601 input.',
        'scheduled_at' => now()->addDay()->toIso8601String(),
    ])->assertOk();

    expect(Schedule::query()->firstWhere('name', 'Timezone Maintenance'))
        ->not->toBeNull()
        ->scheduled_at->not->toBeNull();
});

it('accepts iso-8601 datetimes when completing a schedule through an update', function () {
    Sanctum::actingAs(User::factory()->create(), ['schedule-updates.manage']);

    $schedule = Schedule::factory()->create([
        'scheduled_at' => now()->subHour(),
        'completed_at' => null,
    ]);

    CachetServer::tool(RecordScheduleUpdate::class, [
        'schedule_id' => $schedule->id,
        'message' => 'Maintenance is complete.',
        'completed_at' => now()->toIso8601String(),
    ])->assertOk();

    expect($schedule->fresh())
        ->completed_at->not->toBeNull()
        ->status->toBe(ScheduleStatusEnum::complete);
});
