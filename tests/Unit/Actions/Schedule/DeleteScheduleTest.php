<?php

use Cachet\Actions\Schedule\DeleteSchedule;
use Cachet\Models\Schedule;

it('can delete schedules', function () {
    $schedule = Schedule::factory()->create();

    app(DeleteSchedule::class)->handle($schedule);

    $this->assertSoftDeleted('schedules', [
        'id' => $schedule->id,
    ]);
});
