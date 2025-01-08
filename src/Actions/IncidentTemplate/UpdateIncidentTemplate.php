<?php

namespace Cachet\Actions\IncidentTemplate;

use Cachet\Data\Requests\IncidentTemplate\UpdateIncidentTemplateRequestData;
use Cachet\Models\IncidentTemplate;

class UpdateIncidentTemplate
{
    /**
     * Handle the action.
     */
    public function handle(IncidentTemplate $incidentTemplate, UpdateIncidentTemplateRequestData $data): IncidentTemplate
    {
        $incidentTemplate->update($data->toArray());

        return $incidentTemplate->fresh();
    }
}
