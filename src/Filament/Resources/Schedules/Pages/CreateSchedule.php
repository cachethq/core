<?php

namespace Cachet\Filament\Resources\Schedules\Pages;

use Cachet\Actions\Schedule\CreateSchedule as CreateScheduleAction;
use Cachet\Data\Requests\Schedule\CreateScheduleRequestData;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Filament\Resources\Schedules\ScheduleResource;
use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateSchedule extends CreateRecord
{
    protected static string $resource = ScheduleResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Transform scheduleComponents to the format expected by the action
        $components = null;
        if (! empty($data['scheduleComponents'])) {
            $components = collect($data['scheduleComponents'])->map(fn ($item) => [
                'id' => $item['component_id'],
                'status' => ComponentStatusEnum::from($item['component_status']),
            ])->all();
        }

        $requestData = CreateScheduleRequestData::from([
            'name' => $data['name'],
            'message' => $data['message'],
            'scheduled_at' => $data['scheduled_at'],
            'completed_at' => $data['completed_at'] ?? null,
            'components' => $components,
        ]);

        return app(CreateScheduleAction::class)->handle($requestData);
    }
}
