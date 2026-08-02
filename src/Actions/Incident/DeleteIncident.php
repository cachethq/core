<?php

namespace Cachet\Actions\Incident;

use Cachet\Models\Incident;

class DeleteIncident
{
    /**
     * Handle the action.
     *
     * The delete is soft, so the incident keeps its updates and component
     * attachments for a restore; the model purges them once it is hard
     * deleted.
     */
    public function handle(Incident $incident): void
    {
        $incident->delete();
    }
}
