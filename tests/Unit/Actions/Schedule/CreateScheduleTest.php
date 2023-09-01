<?php

use Cachet\Actions\Schedule\CreateSchedule;
use Cachet\Models\Component;

it('can create a schedule without components', function () {
    $data = [
        'name' => 'My Scheduled Maintenance',
        'message' => 'Something will go down...',
        'status' => 0, // Upcoming...
        'scheduled_at' => '2023-09-01 12:00:00',
    ];

    $schedule = CreateSchedule::run($data);

    expect($schedule)
        ->name->toBe($data['name'])
        ->message->toBe($data['message'])
        ->components->toBeEmpty();
});

it('can create a schedule with components', function () {
    $data = [
        'name' => 'My Scheduled Maintenance',
        'message' => 'Something will go down...',
        'status' => 0, // Upcoming...
        'scheduled_at' => '2023-09-01 12:00:00',
    ];

    [$componentA, $componentB] = Component::factory()->count(2)->create();

    $schedule = CreateSchedule::run($data, [
        ['id' => $componentA->id, 'status' => 3], // Partial Outage
        ['id' => $componentB->id, 'status' => 4], // Major Outage
    ]);

    expect($schedule)
        ->name->toBe($data['name'])
        ->message->toBe($data['message'])
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
