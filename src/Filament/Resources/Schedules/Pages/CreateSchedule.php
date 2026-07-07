<?php

namespace Cachet\Filament\Resources\Schedules\Pages;

use Cachet\Actions\Schedule\NotifyScheduleSubscribers;
use Cachet\Filament\Resources\Schedules\ScheduleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSchedule extends CreateRecord
{
    protected static string $resource = ScheduleResource::class;

    protected function afterCreate(): void
    {
        /** @var \Cachet\Models\Schedule $schedule */
        $schedule = $this->record;

        app(NotifyScheduleSubscribers::class)->handle($schedule);
    }
}
