<?php

namespace Cachet\Actions\Schedule;

use Cachet\Models\Schedule;
use Cachet\Verbs\Events\Schedules\ScheduleDeleted;

class DeleteSchedule
{
    /**
     * Handle the action.
     */
    public function handle(Schedule $schedule): void
    {
        ScheduleDeleted::commit(schedule_id: $schedule->id);
    }
}
