<?php

declare(strict_types=1);

namespace Cachet\Actions\Schedule;

use Cachet\Models\Schedule;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteSchedule
{
    use AsAction;

    public function handle(Schedule $schedule)
    {
        $schedule->delete();
    }
}
