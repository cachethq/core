<?php

namespace Cachet\Actions\Update;

use Cachet\Actions\Incident\UpdateIncident;
use Cachet\Models\Incident;
use Cachet\Models\Update;

class CreateUpdate
{
    /**
     * Handle the action.
     */
    public function handle(Incident $incident, array $data): Update
    {
        $update = new Update(array_merge(['user_id' => auth()->id()], $data));

        $incident->updates()->save($update);

        // Update the incident with the new status.
        if ($incident->status !== $data['status']) {
            app(UpdateIncident::class)->handle($incident, [
                'status' => $data['status'],
            ]);
        }

        // @todo Dispatch notification that incident was updated.

        return $update;
    }
}
