<?php

namespace Cachet\Actions\Update;

use Cachet\Actions\Incident\SyncIncidentStatus;
use Cachet\Actions\Schedule\NotifyScheduleCompletedSubscribers;
use Cachet\Data\Requests\Incident\IncidentComponentRequestData;
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
     */
    public function handle(Incident|Schedule $resource, CreateIncidentUpdateRequestData|CreateScheduleUpdateRequestData $data, ?Authenticatable $user = null): Update
    {
        $update = new Update(array_merge(
            ['user_id' => $user?->getAuthIdentifier()],
            $data->except('completedAt', 'components')->toArray()
        ));

        DB::transaction(function () use ($resource, $update, $data): void {
            $resource->updates()->save($update);

            if ($resource instanceof Incident) {
                $this->syncIncidentStatus->handle($resource);
                $this->syncIncidentComponents($resource, $data);
            }
        });

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
     * Apply the component statuses carried by an incident update.
     *
     * Recording an update is often how operators move the affected components
     * on, so the update can carry new statuses for them. Components not already
     * impacted by the incident are attached to it.
     */
    private function syncIncidentComponents(Incident $incident, CreateIncidentUpdateRequestData|CreateScheduleUpdateRequestData $data): void
    {
        if (! $data instanceof CreateIncidentUpdateRequestData || $data->components === []) {
            return;
        }

        $components = collect($data->components)
            ->mapWithKeys(fn (IncidentComponentRequestData $component): array => [
                $component->id => ['component_status' => $component->status->value],
            ]);

        $incident->components()->syncWithoutDetaching($components->all());
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
}
