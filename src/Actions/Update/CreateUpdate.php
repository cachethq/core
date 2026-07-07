<?php

namespace Cachet\Actions\Update;

use Cachet\Data\Requests\IncidentUpdate\CreateIncidentUpdateRequestData;
use Cachet\Data\Requests\ScheduleUpdate\CreateScheduleUpdateRequestData;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Models\Incident;
use Cachet\Models\Schedule;
use Cachet\Models\Update;

class CreateUpdate
{
    public function __construct(private NotifyIncidentUpdateSubscribers $notifyIncidentUpdateSubscribers)
    {
        //
    }

    /**
     * Handle the action.
     */
    public function handle(Incident|Schedule $resource, CreateIncidentUpdateRequestData|CreateScheduleUpdateRequestData $data): Update
    {
        $update = new Update(array_merge(['user_id' => auth()->id()], $data->toArray()));

        $resource->updates()->save($update);

        if ($resource instanceof Incident && $data->status === IncidentStatusEnum::fixed) {
            $resource->update(['status' => IncidentStatusEnum::fixed]);
            $this->updateComponentsToOperational($resource);
        }

        $this->notifyIncidentUpdateSubscribers->handle($update);

        return $update;
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
