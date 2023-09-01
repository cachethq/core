<?php

use Cachet\Actions\Schedule\DeleteSchedule;
use Cachet\Models\Schedule;

it('can delete schedules', function () {
    $schedule = Schedule::factory()->create();

    DeleteSchedule::run($schedule);

    expect(Schedule::find($schedule->id))->toBeNull();
});
