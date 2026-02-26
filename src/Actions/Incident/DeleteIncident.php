<?php

namespace Cachet\Actions\Incident;

use Cachet\Models\Incident;
use Cachet\Verbs\Events\Incidents\IncidentDeleted;

class DeleteIncident
{
    /**
     * Handle the action.
     */
    public function handle(Incident $incident): void
    {
        IncidentDeleted::commit(incident_id: $incident->id);
    }
}
