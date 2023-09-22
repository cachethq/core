<?php

declare(strict_types=1);

namespace Cachet\Actions\IncidentUpdate;

use Cachet\Actions\Incident\UpdateIncident;
use Cachet\Models\Incident;
use Cachet\Models\IncidentUpdate;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateIncidentUpdate
{
    use AsAction;

    public function handle(Incident $incident, array $data): IncidentUpdate
    {
        $incidentUpdate = $incident->incidentUpdates()->create(array_merge(['user_id' => auth()->id()], $data));

        // Update the incident with the new status.
        if ($incident->status !== $data['status']) {
            UpdateIncident::run($incidentUpdate->incident, [
                'status' => $data['status'],
            ]);
        }

        return $incidentUpdate;
    }
}
