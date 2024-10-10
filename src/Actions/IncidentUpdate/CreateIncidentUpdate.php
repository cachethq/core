<?php

namespace Cachet\Actions\IncidentUpdate;

use Cachet\Actions\Incident\UpdateIncident;
use Cachet\Models\Incident;
use Cachet\Models\IncidentUpdate;

class CreateIncidentUpdate
{
    /**
     * Handle the action.
     */
    public function handle(Incident $incident, array $data): IncidentUpdate
    {
        $incidentUpdate = $incident->incidentUpdates()->create(array_merge(['user_id' => auth()->id()], $data));

        // Update the incident with the new status.
        if ($incident->status !== $data['status']) {
            app(UpdateIncident::class)->handle($incidentUpdate->incident, [
                'status' => $data['status'],
            ]);
        }

        // @todo Dispatch notification that incident was updated.

        return $incidentUpdate;
    }
}
