<?php

namespace Cachet\Actions\Schedule;

use Cachet\Data\Requests\Schedule\CreateScheduleRequestData;
use Cachet\Data\Requests\Schedule\ScheduleComponentRequestData;
use Cachet\Models\Schedule;

class CreateSchedule
{
    /**
     * Handle the action.
     */
    public function handle(CreateScheduleRequestData $data): Schedule
    {

        /** @phpstan-ignore-next-line argument.type */
        return tap(Schedule::create($data->except('components')->toArray()), function (Schedule $schedule) use ($data) {
            if (! $data->components) {
                return;
            }

            $components = collect($data->components)->map(fn (ScheduleComponentRequestData $component) => [
                'component_id' => $component->id,
                'component_status' => $component->status,
            ])->all();

            $schedule->components()->sync($components);

            // @todo Dispatch notification that maintenance was scheduled.
        });
    }
}
