<?php

namespace Cachet\Actions\Update;

use Cachet\Actions\Incident\UpdateIncident;
use Cachet\Models\Incident;
use Cachet\Models\Schedule;
use Cachet\Models\Update;

class CreateUpdate
{
    /**
     * Handle the action.
     */
    public function handle(Incident|Schedule $resource, array $data): Update
    {
        $update = new Update(array_merge(['user_id' => auth()->id()], $data));

        $resource->updates()->save($update);

        // Update the incident with the new status.
        if ($resource instanceof Incident && $resource->status !== $data['status']) {
            app(UpdateIncident::class)->handle($resource, [
                'status' => $data['status'],
            ]);
        }

        // @todo Dispatch notification that incident was updated.

        return $update;
    }
}
