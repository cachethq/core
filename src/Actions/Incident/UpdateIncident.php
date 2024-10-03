<?php

namespace Cachet\Actions\Incident;

use Cachet\Events\Incidents\IncidentUpdated;
use Cachet\Models\Incident;

class UpdateIncident
{
    public function handle(Incident $incident, array $data): Incident
    {
        $incident->update($data);

        IncidentUpdated::dispatch($incident);

        return $incident->fresh();
    }
}
