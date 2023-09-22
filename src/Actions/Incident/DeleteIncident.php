<?php

declare(strict_types=1);

namespace Cachet\Actions\Incident;

use Cachet\Models\Incident;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteIncident
{
    use AsAction;

    public function handle(Incident $incident)
    {
        $incident->incidentUpdates()->delete();

        $incident->delete();
    }
}
