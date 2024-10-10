<?php

namespace Cachet\Actions\Schedule;

use Cachet\Models\Schedule;

class DeleteSchedule
{
    /**
     * Handle the action.
     */
    public function handle(Schedule $schedule): void
    {
        $schedule->delete();
    }
}
