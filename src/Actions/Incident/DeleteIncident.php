<?php

namespace Cachet\Actions\Incident;

use Cachet\Models\Incident;

class DeleteIncident
{
    public function handle(Incident $incident)
    {
        $incident->incidentUpdates()->delete();

        $incident->delete();
    }
}
