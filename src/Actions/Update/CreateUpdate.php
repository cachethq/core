<?php

namespace Cachet\Actions\Update;

use Cachet\Data\Requests\IncidentUpdate\CreateIncidentUpdateRequestData;
use Cachet\Data\Requests\ScheduleUpdate\CreateScheduleUpdateRequestData;
use Cachet\Models\Incident;
use Cachet\Models\Schedule;
use Cachet\Models\Update;
use Cachet\Verbs\Events\Incidents\IncidentUpdateRecorded;
use Cachet\Verbs\Events\Schedules\ScheduleUpdateRecorded;

class CreateUpdate
{
    /**
     * Handle the action.
     */
    public function handle(Incident|Schedule $resource, CreateIncidentUpdateRequestData|CreateScheduleUpdateRequestData $data): Update
    {
        if ($resource instanceof Incident) {
            /** @var CreateIncidentUpdateRequestData $data */
            return IncidentUpdateRecorded::commit(
                incident_id: $resource->id,
                status: $data->status,
                message: $data->message,
                user_id: $data->userId ?? auth()->id(),
            );
        }

        /** @var CreateScheduleUpdateRequestData $data */
        return ScheduleUpdateRecorded::commit(
            schedule_id: $resource->id,
            message: $data->message,
            status: $data->status ?? null,
            user_id: auth()->id(),
        );
    }
}
