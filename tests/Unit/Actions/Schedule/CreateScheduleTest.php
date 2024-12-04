<?php

use Cachet\Actions\Schedule\CreateSchedule;
use Cachet\Models\Component;

it('can create a schedule without components', function () {
    $data = [
        'name' => 'My Scheduled Maintenance',
        'message' => 'Something will go down...',
        'scheduled_at' => '2023-09-01 12:00:00',
        'completed_at' => '2023-10-01 12:00:00',
    ];

    $schedule = app(CreateSchedule::class)->handle($data);

    expect($schedule)
        ->name->toBe($data['name'])
        ->message->toBe($data['message'])
        ->components->toBeEmpty();
});

it('can create a schedule with components', function () {
    $data = [
        'name' => 'My Scheduled Maintenance',
        'message' => 'Something will go down...',
        'scheduled_at' => '2023-09-01 12:00:00',
        'completed_at' => '2023-10-01 12:00:00',
    ];

    [$componentA, $componentB] = Component::factory()->count(2)->create();

    $schedule = app(CreateSchedule::class)->handle($data, [
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
