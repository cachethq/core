<?php

use Cachet\Enums\ScheduleStatusEnum;
use Cachet\Models\Schedule;
use Cachet\Models\ScheduleComponent;

it('has components', function () {
    $schedule = Schedule::factory()->create();
    ScheduleComponent::factory()
        ->count(2)
        ->create([
            'schedule_id' => $schedule->id,
        ]);

    expect($schedule)
        ->components->toHaveCount(2);
});

it('can get incomplete schedules', function () {
    $scheduleA = Schedule::factory()->inTheFuture()->create();
    Schedule::factory()->inProgress()->create();
    Schedule::factory()->inThePast()->create();

    expect(Schedule::query()->incomplete()->get())
        ->toHaveCount(2)
        ->first()->id->toBe($scheduleA->id);
});

it('can get in-progress schedules', function () {
    $schedule = Schedule::factory()->inThePast()->create();
    $scheduleInProgress = Schedule::factory()->inProgress()->create();

    expect(Schedule::query()->inProgress()->get())
        ->toHaveCount(1)
        ->first()->id->toBe($scheduleInProgress->id);
});

it('can get schedules in the future', function () {
    $schedule = Schedule::factory()->inThePast()->create();
    $scheduleInFuture = Schedule::factory()->inTheFuture()->create();

    expect(Schedule::query()->inTheFuture()->get())
        ->toHaveCount(1)
        ->first()->id->toBe($scheduleInFuture->id);
});

it('can get schedules from the past', function () {
    Schedule::factory()->inTheFuture()->create();
    $scheduleInPast = Schedule::factory()->inThePast()->create();

    expect(Schedule::query()->inThePast()->get())
        ->toHaveCount(1)
        ->first()->id->toBe($scheduleInPast->id);
});