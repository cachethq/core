<?php

namespace Cachet\Actions\Incident;

use Cachet\Data\Requests\Incident\UpdateIncidentRequestData;
use Cachet\Models\Incident;
use Cachet\Verbs\Events\Incidents\IncidentUpdated;

class UpdateIncident
{
    /**
     * Handle the action.
     */
    public function handle(Incident $incident, UpdateIncidentRequestData $data): Incident
    {
        IncidentUpdated::commit(
            incident_id: $incident->id,
            name: $data->name,
            status: $data->status,
            message: $data->message,
            visible: $data->visible,
            stickied: $data->stickied,
            notifications: $data->notifications,
            occurred_at: $data->occurredAt,
        );

        // Refresh the original model with updated data
        $incident->refresh();

        return $incident;
    }
}
