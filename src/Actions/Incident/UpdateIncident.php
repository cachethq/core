<?php

namespace Cachet\Actions\Incident;

use Cachet\Models\Incident;

class UpdateIncident
{
    public function handle(Incident $incident, array $data): Incident
    {
        $incident->update($data);

        return $incident->fresh();
    }
}
