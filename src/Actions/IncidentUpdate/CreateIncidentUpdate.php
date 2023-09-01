<?php

namespace Cachet\Actions\IncidentUpdate;

use Cachet\Models\Incident;
use Cachet\Models\IncidentUpdate;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateIncidentUpdate
{
    use AsAction;

    public function handle(Incident $incident, array $data): IncidentUpdate
    {
        return $incident->incidentUpdates()->create(array_merge(['user_id' => auth()->id()], $data));
    }
}
