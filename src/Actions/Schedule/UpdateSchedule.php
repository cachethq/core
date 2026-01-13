<?php

namespace Cachet\Actions\Schedule;

use Cachet\Data\Requests\Schedule\ScheduleComponentRequestData;
use Cachet\Data\Requests\Schedule\UpdateScheduleRequestData;
use Cachet\Models\Schedule;
use Cachet\Verbs\Events\Schedules\ComponentAttachedToSchedule;
use Cachet\Verbs\Events\Schedules\ComponentDetachedFromSchedule;
use Cachet\Verbs\Events\Schedules\ScheduleUpdated;

class UpdateSchedule
{
    /**
     * Handle the action.
     */
    public function handle(Schedule $schedule, UpdateScheduleRequestData $data): Schedule
    {
        $result = ScheduleUpdated::commit(
            schedule_id: $schedule->id,
            name: $data->name,
            message: $data->message,
            scheduled_at: $data->scheduledAt,
        );

        if ($data->components) {
            $currentComponentIds = $schedule->components()->pluck('components.id')->all();
            $newComponentIds = collect($data->components)->pluck('id')->all();

            // Detach removed components
            foreach (array_diff($currentComponentIds, $newComponentIds) as $componentId) {
                ComponentDetachedFromSchedule::commit(
                    schedule_id: $schedule->id,
                    component_id: $componentId,
                );
            }

            // Attach new components
            foreach ($data->components as $component) {
                /** @var ScheduleComponentRequestData $component */
                if (! in_array($component->id, $currentComponentIds)) {
                    ComponentAttachedToSchedule::commit(
                        schedule_id: $schedule->id,
                        component_id: $component->id,
                        component_status: $component->status,
                    );
                }
            }
        }

        // Refresh the original model with updated data
        $schedule->refresh();

        return $schedule;
    }
}
