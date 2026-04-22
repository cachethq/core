<?php

use Cachet\Enums\ScheduleStatusEnum;
use Cachet\Filters\ScheduleStatusFilter;
use Cachet\Models\Schedule;

it('filters by a numeric status value', function () {
    $upcoming = Schedule::factory()->inTheFuture()->create();
    Schedule::factory()->inProgress()->create();
    Schedule::factory()->inThePast()->create();

    $query = Schedule::query();

    (new ScheduleStatusFilter)($query, ScheduleStatusEnum::upcoming->value, 'status');

    expect($query->get())
        ->toHaveCount(1)
        ->first()->id->toBe($upcoming->id);
});

it('filters by an enum instance', function () {
    Schedule::factory()->inTheFuture()->create();
    $inProgress = Schedule::factory()->inProgress()->create();
    Schedule::factory()->inThePast()->create();

    $query = Schedule::query();

    (new ScheduleStatusFilter)($query, ScheduleStatusEnum::in_progress, 'status');

    expect($query->get())
        ->toHaveCount(1)
        ->first()->id->toBe($inProgress->id);
});

it('leaves the query untouched when given an invalid value', function () {
    Schedule::factory()->inTheFuture()->create();
    Schedule::factory()->inProgress()->create();
    Schedule::factory()->inThePast()->create();

    $query = Schedule::query();

    (new ScheduleStatusFilter)($query, 999, 'status');

    expect($query->get())->toHaveCount(3);
});

it('leaves the query untouched when given a non-numeric string', function () {
    Schedule::factory()->inTheFuture()->create();
    Schedule::factory()->inProgress()->create();

    $query = Schedule::query();

    (new ScheduleStatusFilter)($query, 'not-a-status', 'status');

    expect($query->get())->toHaveCount(2);
});

it('filters by multiple statuses', function () {
    Schedule::factory()->inTheFuture()->create();
    $inProgress = Schedule::factory()->inProgress()->create();
    $completed = Schedule::factory()->completed()->create();

    $query = Schedule::query();

    (new ScheduleStatusFilter)($query, [
        ScheduleStatusEnum::in_progress->value,
        ScheduleStatusEnum::complete->value,
    ], 'status');

    expect($query->pluck('id')->all())
        ->toHaveCount(2)
        ->toContain($inProgress->id, $completed->id);
});

it('ignores invalid entries in a multi-value filter', function () {
    Schedule::factory()->inTheFuture()->create();
    $inProgress = Schedule::factory()->inProgress()->create();
    Schedule::factory()->inThePast()->create();

    $query = Schedule::query();

    (new ScheduleStatusFilter)($query, [
        ScheduleStatusEnum::in_progress->value,
        999,
        'bogus',
    ], 'status');

    expect($query->get())
        ->toHaveCount(1)
        ->first()->id->toBe($inProgress->id);
});

it('applies no status constraint when every multi-value entry is invalid', function () {
    Schedule::factory()->inTheFuture()->create();
    Schedule::factory()->inProgress()->create();
    Schedule::factory()->inThePast()->create();

    $query = Schedule::query();

    (new ScheduleStatusFilter)($query, [999, 'bogus'], 'status');

    expect($query->get())->toHaveCount(3);
});
