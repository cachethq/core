<?php

namespace Cachet\Actions\IncidentUpdate;

use Cachet\Models\IncidentUpdate;

class DeleteIncidentUpdate
{
    /**
     * Handle the action.
     */
    public function handle(IncidentUpdate $incidentUpdate): void
    {
        $incidentUpdate->delete();
    }
}
