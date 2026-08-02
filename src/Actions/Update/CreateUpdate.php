<?php

namespace Cachet\Actions\Update;

use Cachet\Actions\Incident\SyncIncidentStatus;
use Cachet\Actions\Schedule\NotifyScheduleCompletedSubscribers;
use Cachet\Data\Requests\IncidentUpdate\CreateIncidentUpdateRequestData;
use Cachet\Data\Requests\ScheduleUpdate\CreateScheduleUpdateRequestData;
use Cachet\Enums\ScheduleStatusEnum;
use Cachet\Models\Incident;
use Cachet\Models\Schedule;
use Cachet\Models\Update;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;

class CreateUpdate
{
    public function __construct(
        private NotifyIncidentUpdateSubscribers $notifyIncidentUpdateSubscribers,
        private NotifyScheduleUpdateSubscribers $notifyScheduleUpdateSubscribers,
        private NotifyScheduleCompletedSubscribers $notifyScheduleCompletedSubscribers,
        private SyncIncidentStatus $syncIncidentStatus,
    ) {
        //
    }

    /**
     * Handle the action.
     *
     * A completion time carried by a schedule update is written in the same
     * transaction as the update itself — quietly, since the update is the
     * communication — so the window can never close without its update, or
     * the other way around.
     */
    public function handle(Incident|Schedule $resource, CreateIncidentUpdateRequestData|CreateScheduleUpdateRequestData $data, ?Authenticatable $user = null): Update
    {
        $update = new Update(array_merge(
            ['user_id' => $user?->getAuthIdentifier()],
            $data->except('completedAt')->toArray()
        ));

        DB::transaction(function () use ($resource, $update, $data): void {
            $resource->updates()->save($update);

            if ($resource instanceof Incident) {
                $this->syncIncidentStatus->handle($resource);
            }

            if ($resource instanceof Schedule && $data instanceof CreateScheduleUpdateRequestData && $data->completedAt !== null) {
                $resource->updateQuietly(['completed_at' => $data->completedAt]);
            }
        });

        $this->notifyIncidentUpdateSubscribers->handle($update);

        if ($resource instanceof Schedule) {
            if ($this->scheduleCompleted($resource, $data)) {
                $this->notifyScheduleCompletedSubscribers->handle($resource);
            } else {
                $this->notifyScheduleUpdateSubscribers->handle($update);
            }
        }

        return $update;
    }

    /**
     * Determine whether the update's completion time actually completed the
     * schedule, in which case the completion notification supersedes the
     * update notification.
     */
    private function scheduleCompleted(Schedule $schedule, CreateIncidentUpdateRequestData|CreateScheduleUpdateRequestData $data): bool
    {
        return $data instanceof CreateScheduleUpdateRequestData
            && $data->completedAt !== null
            && $schedule->status === ScheduleStatusEnum::complete;
    }
}
