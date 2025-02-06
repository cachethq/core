<?php

namespace Cachet\Actions\Schedule;

use Cachet\Data\Requests\Schedule\CreateScheduleRequestData;
use Cachet\Data\Requests\Schedule\ScheduleComponentRequestData;
use Cachet\Models\Schedule;
use Illuminate\Database\Eloquent\Model;

class CreateSchedule
{
    /**
     * Handle the action.
     */
    public function handle(CreateScheduleRequestData $data): Schedule
    {
        /** @var Schedule $model */
        $model = tap(Schedule::create($data->except('components')->toArray()), function (Model $schedule) use ($data) {
            /** @var Schedule $schedule */
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
        return $model;
    }
}
