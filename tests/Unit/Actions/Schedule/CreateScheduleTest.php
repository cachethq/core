<?php

use Cachet\Actions\Schedule\CreateSchedule;
use Cachet\Data\Requests\Schedule\CreateScheduleRequestData;
use Cachet\Models\Component;

it('can create a schedule without components', function () {
    $data = CreateScheduleRequestData::from([
        'name' => 'My Scheduled Maintenance',
        'message' => 'Something will go down...',
        'scheduled_at' => '2023-09-01 12:00:00',
        'completed_at' => '2023-10-01 12:00:00',
    ]);

    $schedule = app(CreateSchedule::class)->handle($data);

    expect($schedule)
        ->name->toBe($data->name)
        ->message->toBe($data->message)
        ->components->toBeEmpty();
});

it('can create a schedule with components', function () {
    [$componentA, $componentB] = Component::factory()->count(2)->create();

    $data = CreateScheduleRequestData::from([
        'name' => 'My Scheduled Maintenance',
        'message' => 'Something will go down...',
        'scheduled_at' => '2023-09-01 12:00:00',
        'completed_at' => '2023-10-01 12:00:00',
        'components' => [
            ['id' => $componentA->id, 'status' => 3], // Partial Outage
            ['id' => $componentB->id, 'status' => 4], // Major Outage
        ],
    ]);

    $schedule = app(CreateSchedule::class)->handle($data);

    expect($schedule)
        ->name->toBe($data->name)
        ->message->toBe($data->message)
        ->components->not->toBeEmpty()
        ->components->toHaveCount(2);

    $this->assertDatabaseHas('schedule_components', [
        'component_id' => $componentA->id,
        'component_status' => 3,
    ]);
    $this->assertDatabaseHas('schedule_components', [
        'component_id' => $componentB->id,
        'component_status' => 4,
    ]);
});
