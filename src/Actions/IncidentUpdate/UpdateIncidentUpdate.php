<?php

namespace Cachet\Actions\IncidentUpdate;

use Cachet\Data\IncidentUpdate\UpdateIncidentUpdateData;
use Cachet\Models\IncidentUpdate;

class UpdateIncidentUpdate
{
    /**
     * Handle the action.
     */
    public function handle(IncidentUpdate $incidentUpdate, UpdateIncidentUpdateData $data): IncidentUpdate
    {
        $incidentUpdate->update($data->toArray());

        return $incidentUpdate->fresh();
    }
}
