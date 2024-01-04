<?php

namespace Cachet\Actions\IncidentUpdate;

use Cachet\Models\IncidentUpdate;

class DeleteIncidentUpdate
{
    public function handle(IncidentUpdate $incidentUpdate)
    {
        $incidentUpdate->delete();
    }
}
