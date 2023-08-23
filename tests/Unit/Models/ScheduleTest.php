<?php

use Cachet\Models\Schedule;
use Cachet\Models\ScheduleComponent;

it('has components', function () {
    $schedule = Schedule::factory()->create();
    $components = ScheduleComponent::factory()
        ->count(2)
        ->create([
            'schedule_id' => $schedule->id,
        ]);

    expect($schedule)
        ->components->toHaveCount(2);
});
