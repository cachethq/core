<?php

namespace Cachet\Actions\Update;

use Cachet\Data\Requests\IncidentUpdate\CreateIncidentUpdateRequestData;
use Cachet\Data\Requests\ScheduleUpdate\CreateScheduleUpdateRequestData;
use Cachet\Events\Incidents\IncidentUpdated;
use Cachet\Models\Incident;
use Cachet\Models\Schedule;
use Cachet\Models\Update;

class CreateUpdate
{
    /**
     * Handle the action.
     */
    public function handle(Incident|Schedule $resource, CreateIncidentUpdateRequestData|CreateScheduleUpdateRequestData $data): Update
    {
        $update = new Update(array_merge(['user_id' => auth()->id()], $data->toArray()));

        $resource->updates()->save($update);

        if($resource instanceof Incident) {
            $resource->update(['status' => $update->status]);
            IncidentUpdated::dispatch($resource);
        }

        return $update;
    }
}
