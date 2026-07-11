<?php

namespace Cachet\Actions\Incident;

use Cachet\Data\Requests\Incident\UpdateIncidentRequestData;
use Cachet\Models\Incident;

class UpdateIncident
{
    /**
     * Handle the action.
     */
    public function handle(Incident $incident, UpdateIncidentRequestData $data): Incident
    {
        $incident->update($data->except('meta')->toArray());

        if ($data->meta !== null) {
            $incident->syncMeta($data->meta);
        }

        return $incident->fresh();
    }
}
