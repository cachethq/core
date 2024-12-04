<?php

namespace Cachet\Actions\IncidentUpdate;

use Cachet\Actions\Incident\UpdateIncident;
use Cachet\Data\Incident\UpdateIncidentData;
use Cachet\Data\IncidentUpdate\CreateIncidentUpdateData;
use Cachet\Models\Incident;
use Cachet\Models\IncidentUpdate;
use Spatie\LaravelData\Optional;

class CreateIncidentUpdate
{
    /**
     * Handle the action.
     */
    public function handle(Incident $incident, CreateIncidentUpdateData $data): IncidentUpdate
    {
        $incidentUpdate = $incident->incidentUpdates()->create(array_merge([
            'user_id' => auth()->id()
        ], $data->toArray()));

        // Update the incident with the new status.
        if ($incident->status !== $data->status) {
            app(UpdateIncident::class)->handle($incidentUpdate->incident, new UpdateIncidentData(
                name: Optional::create(),
                status: $data->status,
            ));
        }

        // @todo Dispatch notification that incident was updated.

        return $incidentUpdate;
    }
}
