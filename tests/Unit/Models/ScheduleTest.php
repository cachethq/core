<?php

use Cachet\Enums\ScheduleStatusEnum;
use Cachet\Models\Schedule;
use Cachet\Models\ScheduleComponent;
use Illuminate\Support\Carbon;

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

it('can get incomplete schedules at midnight', function () {
    Carbon::setTestNow(Carbon::create(2024, 12, 23, 0));

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

it('can determine a schedule\'s upcoming status', function () {
    $schedule = Schedule::factory()->inTheFuture()->create();

    expect($schedule)->status->toBe(ScheduleStatusEnum::upcoming);
});

it('can determine a schedule\'s in-progress status', function () {
    $schedule = Schedule::factory()->inProgress()->create();

    expect($schedule)->status->toBe(ScheduleStatusEnum::in_progress);
});

it('can determine a schedule\'s completed status', function () {
    $schedule = Schedule::factory()->completed()->create();

    expect($schedule)->status->toBe(ScheduleStatusEnum::complete);
});
