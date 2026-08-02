<?php

use Cachet\Actions\Schedule\UpdateSchedule;
use Cachet\Data\Requests\Schedule\UpdateScheduleRequestData;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Models\Component;
use Cachet\Models\Meta;
use Cachet\Models\Schedule;

it('can update a schedule', function () {
    $schedule = Schedule::factory()->create();

    $data = UpdateScheduleRequestData::from([
        'name' => 'Schedule Updated',
    ]);

    app(UpdateSchedule::class)->handle($schedule, $data);

    expect($schedule)
        ->name->toBe($data->name);
});

it('can update a schedule with components', function () {
    $schedule = Schedule::factory()->create();
    [$componentA, $componentB] = Component::factory()->count(2)->create();

    $data = UpdateScheduleRequestData::from([
        'components' => [
            ['id' => $componentA->id, 'status' => ComponentStatusEnum::performance_issues],
            ['id' => $componentB->id, 'status' => ComponentStatusEnum::major_outage],
        ],
    ]);

    app(UpdateSchedule::class)->handle($schedule, $data);

    expect($schedule)
        ->components->not->toBeEmpty()
        ->components->toHaveCount(2);

    $this->assertDatabaseHas('schedule_components', [
        'component_id' => $componentA->id,
        'component_status' => ComponentStatusEnum::performance_issues,
    ]);
    $this->assertDatabaseHas('schedule_components', [
        'component_id' => $componentB->id,
        'component_status' => ComponentStatusEnum::major_outage,
    ]);
});

it('rolls the whole update back when a write fails part-way', function () {
    $schedule = Schedule::factory()->create(['name' => 'Original Name']);

    Meta::creating(fn () => throw new RuntimeException('boom'));

    try {
        app(UpdateSchedule::class)->handle($schedule, UpdateScheduleRequestData::from([
            'name' => 'Updated Name',
            'meta' => ['cluster' => 'eu-west'],
        ]));
    } catch (RuntimeException) {
        //
    }

    expect($schedule->fresh()->name)->toBe('Original Name');
});
