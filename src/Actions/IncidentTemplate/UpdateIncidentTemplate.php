<?php

namespace Cachet\Actions\IncidentTemplate;

use Cachet\Models\IncidentTemplate;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateIncidentTemplate
{
    use AsAction;

    public function handle(IncidentTemplate $incidentTemplate, array $data): IncidentTemplate
    {
        $incidentTemplate->update($data);

        return $incidentTemplate->fresh();
    }
}
