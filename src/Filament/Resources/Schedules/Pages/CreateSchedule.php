<?php

namespace Cachet\Filament\Resources\Schedules\Pages;

use Cachet\Actions\Schedule\NotifyScheduleSubscribers;
use Cachet\Filament\Concerns\InteractsWithMeta;
use Cachet\Filament\Resources\Schedules\ScheduleResource;
use Cachet\Models\Schedule;
use Filament\Resources\Pages\CreateRecord;

class CreateSchedule extends CreateRecord
{
    use InteractsWithMeta;

    protected static string $resource = ScheduleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->extractMetaFormData($data);
    }

    protected function afterCreate(): void
    {
        /** @var Schedule $schedule */
        $schedule = $this->record;

        $this->persistMeta();

        app(NotifyScheduleSubscribers::class)->handle($schedule);
    }
}
