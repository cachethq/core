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
        $attributes = $data->except('meta')->toArray();

        if ($data->status !== null) {
            $attributes['baseline_status'] = $data->status;
        }

        $incident->update($attributes);

        if ($data->meta !== null) {
            $incident->syncMeta($data->meta);
        }

        return $incident->fresh();
    }
}
