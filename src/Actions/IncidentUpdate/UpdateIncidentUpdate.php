<?php

namespace Cachet\Actions\IncidentUpdate;

use Cachet\Models\IncidentUpdate;

class UpdateIncidentUpdate
{
    /**
     * Handle the action.
     */
    public function handle(IncidentUpdate $incidentUpdate, array $data): IncidentUpdate
    {
        $incidentUpdate->update($data);

        return $incidentUpdate->fresh();
    }
}
