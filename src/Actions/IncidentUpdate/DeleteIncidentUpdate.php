<?php

namespace Cachet\Actions\IncidentUpdate;

use Cachet\Models\IncidentUpdate;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteIncidentUpdate
{
    use AsAction;

    public function handle(IncidentUpdate $incidentUpdate)
    {
        $incidentUpdate->delete();
    }
}
