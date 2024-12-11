<?php

namespace Cachet\Actions\Incident;

use Cachet\Data\Incident\UpdateIncidentData;
use Cachet\Events\Incidents\IncidentUpdated;
use Cachet\Models\Incident;

class UpdateIncident
{
    /**
     * Handle the action.
     */
    public function handle(Incident $incident, UpdateIncidentData $data): Incident
    {
        $incident->update($data->toArray());

        IncidentUpdated::dispatch($incident);

        return $incident->fresh();
    }
}
