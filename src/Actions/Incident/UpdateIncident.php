<?php

namespace Cachet\Actions\Incident;

use Cachet\Data\Requests\Incident\UpdateIncidentRequestData;
use Cachet\Events\Incidents\IncidentUpdated;
use Cachet\Models\Incident;

class UpdateIncident
{
    /**
     * Handle the action.
     */
    public function handle(Incident $incident, UpdateIncidentRequestData $data): Incident
    {
        $incident->update($data->toArray());

        IncidentUpdated::dispatch($incident);

        return $incident->fresh();
    }
}
