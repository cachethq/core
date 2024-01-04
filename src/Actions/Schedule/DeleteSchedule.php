<?php

namespace Cachet\Actions\Schedule;

use Cachet\Models\Schedule;

class DeleteSchedule
{
    public function handle(Schedule $schedule)
    {
        $schedule->delete();
    }
}
