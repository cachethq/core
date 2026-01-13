<?php

namespace Cachet\Actions\Schedule;

use Cachet\Data\Requests\Schedule\CreateScheduleRequestData;
use Cachet\Data\Requests\Schedule\ScheduleComponentRequestData;
use Cachet\Models\Schedule;
use Cachet\Verbs\Events\Schedules\ScheduleCreated;

class CreateSchedule
{
    /**
     * Handle the action.
     */
    public function handle(CreateScheduleRequestData $data): Schedule
    {
        $components = [];
        if ($data->components) {
            $components = collect($data->components)->map(fn (ScheduleComponentRequestData $component) => [
                'id' => $component->id,
                'status' => $component->status,
            ])->all();
        }

        return ScheduleCreated::commit(
            name: $data->name,
            message: $data->message,
            scheduled_at: $data->scheduledAt->toIso8601String(),
            completed_at: $data->completedAt?->toIso8601String(),
            components: $components,
        );
    }
}
