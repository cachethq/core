<?php

namespace Cachet\Actions\IncidentTemplate;

use Cachet\Models\IncidentTemplate;

class UpdateIncidentTemplate
{
    public function handle(IncidentTemplate $incidentTemplate, array $data): IncidentTemplate
    {
        $incidentTemplate->update($data);

        return $incidentTemplate->fresh();
    }
}
