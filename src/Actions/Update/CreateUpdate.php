<?php

namespace Cachet\Actions\Update;

use Cachet\Data\IncidentUpdate\CreateIncidentUpdateRequestData;
use Cachet\Data\ScheduleUpdate\CreateScheduleUpdateRequestData;
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

        // @todo Dispatch notification that incident was updated.

        return $update;
    }
}
