<?php

declare(strict_types=1);

namespace Cachet\Actions\Incident;

use Cachet\Models\Incident;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateIncident
{
    use AsAction;

    public function handle(Incident $incident, array $data): Incident
    {
        $incident->update($data);

        return $incident->fresh();
    }
}
