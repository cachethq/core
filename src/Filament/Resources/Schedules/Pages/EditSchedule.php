<?php

namespace Cachet\Filament\Resources\Schedules\Pages;

use Cachet\Actions\Schedule\UpdateSchedule;
use Cachet\Data\Requests\Schedule\UpdateScheduleRequestData;
use Cachet\Filament\Resources\Schedules\ScheduleResource;
use Cachet\Models\Schedule;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditSchedule extends EditRecord
{
    protected static string $resource = ScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var Schedule $record */
        $requestData = UpdateScheduleRequestData::from($data);

        app(UpdateSchedule::class)->handle($record, $requestData);

        return $record;
    }
}
