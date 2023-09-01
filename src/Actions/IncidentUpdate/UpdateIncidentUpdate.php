<?php

namespace Cachet\Actions\IncidentUpdate;

use Cachet\Models\IncidentUpdate;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateIncidentUpdate
{
    use AsAction;

    public function handle(IncidentUpdate $incidentUpdate, array $data): IncidentUpdate
    {
        $incidentUpdate->update($data);

        return $incidentUpdate->fresh();
    }
}
