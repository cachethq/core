<?php

namespace Cachet\Actions\Schedule;

use Cachet\Data\Schedule\CreateScheduleData;
use Cachet\Data\Schedule\ScheduleComponentData;
use Cachet\Models\Schedule;

class CreateSchedule
{
    /**
     * Handle the action.
     */
    public function handle(CreateScheduleData $data): Schedule
    {
        return tap(Schedule::create($data->except('components')->toArray()), function (Schedule $schedule) use ($data) {
            if (! $data->components) {
                return;
            }

            $components = collect($data->components)->map(fn (ScheduleComponentData $component) => [
                'component_id' => $component->id,
                'component_status' => $component->status,
            ])->all();

            $schedule->components()->sync($components);

            // @todo Dispatch notification that maintenance was scheduled.
        });
    }
}
