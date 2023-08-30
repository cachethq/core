<?php

namespace Cachet\Actions\Incident;

use Cachet\Models\Incident;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteIncident
{
    use AsAction;

    public function handle(Incident $incident)
    {
        // @todo remove updates with incident.

        $incident->delete();
    }
}
