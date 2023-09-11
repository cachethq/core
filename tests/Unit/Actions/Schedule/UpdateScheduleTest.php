<?php

use Cachet\Actions\Schedule\UpdateSchedule;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Models\Component;
use Cachet\Models\Schedule;

it('can update a schedule', function () {
    $schedule = Schedule::factory()->create();

    $data = [
        'name' => 'Schedule Updated',
    ];

    UpdateSchedule::run($schedule, $data);

    expect($schedule)
        ->name->toBe($data['name']);
});

it('can update a schedule with components', function () {
    $schedule = Schedule::factory()->create();
    [$componentA, $componentB] = Component::factory()->count(2)->create();

    UpdateSchedule::run($schedule, [], [
        ['id' => $componentA->id, 'status' => ComponentStatusEnum::performance_issues],
        ['id' => $componentB->id, 'status' => ComponentStatusEnum::major_outage],
    ]);

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
