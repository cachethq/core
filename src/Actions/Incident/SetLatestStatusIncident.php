<?php

namespace Cachet\Actions\Incident;

use Cachet\Enums\IncidentStatusEnum;
use Cachet\Events\Incidents\IncidentUpdated;
use Cachet\Models\Incident;
use Cachet\Models\Update;

class SetLatestStatusIncident
{
    /**
     * Handle the action.
     */
    public function handle(Incident $incident): Incident
    {
        /** @var Update|null $update */
        $update = $incident->updates()->latest()->limit(1)->first();

        if(is_null($update) && $incident->status === IncidentStatusEnum::unknown) {
            return $incident;
        }

        $newStatus = $update->status ?? IncidentStatusEnum::unknown;

        if($newStatus === $incident->status) {
            return $incident;
        }


        $incident->update(['status' => $newStatus]);
        IncidentUpdated::dispatch($incident);
        return $incident->fresh();
    }
}
