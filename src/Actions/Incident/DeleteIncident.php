<?php

namespace Cachet\Actions\Incident;

use Cachet\Models\Incident;

class DeleteIncident
{
    /**
     * Handle the action.
     */
    public function handle(Incident $incident): void
    {
        $incident->incidentUpdates()->delete();

        $incident->delete();
    }
}
