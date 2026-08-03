<?php

namespace Cachet\Actions\Incident;

use Cachet\Data\Requests\Incident\UpdateIncidentRequestData;
use Cachet\Models\Incident;
use Illuminate\Support\Facades\DB;

class UpdateIncident
{
    /**
     * Handle the action.
     */
    public function handle(Incident $incident, UpdateIncidentRequestData $data): Incident
    {
        DB::transaction(function () use ($incident, $data): void {
            $incident->update($data->except('meta')->toArray());

            if (is_array($data->meta)) {
                $incident->syncMeta($data->meta);
            }
        });

        return $incident->fresh();
    }
}
