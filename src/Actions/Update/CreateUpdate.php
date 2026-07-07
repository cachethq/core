<?php

namespace Cachet\Actions\Update;

use Cachet\Actions\Schedule\NotifyScheduleCompletedSubscribers;
use Cachet\Data\Requests\IncidentUpdate\CreateIncidentUpdateRequestData;
use Cachet\Data\Requests\ScheduleUpdate\CreateScheduleUpdateRequestData;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Enums\ScheduleStatusEnum;
use Cachet\Models\Incident;
use Cachet\Models\Schedule;
use Cachet\Models\Update;

class CreateUpdate
{
    public function __construct(
        private NotifyIncidentUpdateSubscribers $notifyIncidentUpdateSubscribers,
        private NotifyScheduleUpdateSubscribers $notifyScheduleUpdateSubscribers,
        private NotifyScheduleCompletedSubscribers $notifyScheduleCompletedSubscribers,
    ) {
        //
    }

    /**
     * Handle the action.
     */
    public function handle(Incident|Schedule $resource, CreateIncidentUpdateRequestData|CreateScheduleUpdateRequestData $data): Update
    {
        $update = new Update(array_merge(['user_id' => auth()->id()], $data->except('completedAt')->toArray()));

        $resource->updates()->save($update);

        if ($resource instanceof Incident && $data->status === IncidentStatusEnum::fixed) {
            $resource->update(['status' => IncidentStatusEnum::fixed]);
            $this->updateComponentsToOperational($resource);
        }

        $this->notifyIncidentUpdateSubscribers->handle($update);

        if ($resource instanceof Schedule) {
            $completed = $this->completeSchedule($resource, $data);

            if (! $completed) {
                $this->notifyScheduleUpdateSubscribers->handle($update);
            }
        }

        return $update;
    }

    /**
     * Complete the schedule when the update provides a completion time.
     *
     * The window change is applied quietly — the update itself is the
     * communication — and returns true when the schedule has actually
     * completed, in which case the completion notification supersedes
     * the update notification.
     */
    private function completeSchedule(Schedule $schedule, CreateIncidentUpdateRequestData|CreateScheduleUpdateRequestData $data): bool
    {
        if (! $data instanceof CreateScheduleUpdateRequestData || $data->completedAt === null) {
            return false;
        }

        $schedule->updateQuietly(['completed_at' => $data->completedAt]);

        if ($schedule->status === ScheduleStatusEnum::complete) {
            $this->notifyScheduleCompletedSubscribers->handle($schedule);

            return true;
        }

        return false;
    }

    /**
     * Set all linked components back to operational when an incident is fixed.
     */
    private function updateComponentsToOperational(Incident $incident): void
    {
        $incident->components()->each(function ($component) use ($incident) {
            $incident->components()->updateExistingPivot($component->id, [
                'component_status' => ComponentStatusEnum::operational,
            ]);
        });
    }
}
