<?php

namespace Cachet\Actions\Schedule;

use Cachet\Data\Requests\Schedule\ScheduleComponentRequestData;
use Cachet\Data\Requests\Schedule\UpdateScheduleRequestData;
use Cachet\Models\Schedule;

class UpdateSchedule
{
    /**
     * Handle the action.
     */
    public function handle(Schedule $schedule, UpdateScheduleRequestData $data): Schedule
    {
        $schedule->update($data->except('components')->toArray());

        if ($data->components) {
            $components = collect($data->components)->map(fn (ScheduleComponentRequestData $component) => [
                'component_id' => $component->id,
                'component_status' => $component->status,
            ])->all();

            $schedule->components()->sync($components);
        }

        // @todo Dispatch notification that maintenance was updated.

        return $schedule->fresh();
    }
}
