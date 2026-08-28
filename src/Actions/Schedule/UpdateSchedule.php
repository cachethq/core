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
        $schedule->update($data->except('components', 'meta', 'tags')->toArray());

        if ($data->meta !== null) {
            $schedule->syncMeta($data->meta);
        }

        if ($data->tags !== null) {
            $schedule->syncTags($data->tags);
        }

        if ($data->components !== null) {
            $components = collect($data->components)
                ->mapWithKeys(fn (ScheduleComponentRequestData $component) => [
                    $component->id => ['component_status' => $component->status],
                ])
                ->all();

            $schedule->components()->sync($components);
        }

        // @todo Dispatch notification that maintenance was updated.

        return $schedule->fresh();
    }
}
