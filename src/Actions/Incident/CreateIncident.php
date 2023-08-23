<?php

namespace Cachet\Actions\Incident;

use Cachet\Data\IncidentData;
use Cachet\Models\Incident;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateIncident
{
    use AsAction;

    public function handle(IncidentData $incident): Incident
    {
        return Incident::create($incident->toArray());
    }
}
