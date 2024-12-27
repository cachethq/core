<?php

namespace Cachet\Actions\IncidentTemplate;

use Cachet\Data\IncidentTemplate\UpdateIncidentTemplateData;
use Cachet\Models\IncidentTemplate;

class UpdateIncidentTemplate
{
    /**
     * Handle the action.
     */
    public function handle(IncidentTemplate $incidentTemplate, UpdateIncidentTemplateData $data): IncidentTemplate
    {
        $incidentTemplate->update($data->toArray());

        return $incidentTemplate->fresh();
    }
}
