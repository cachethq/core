<?php

namespace Cachet\Actions\IncidentTemplate;

use Cachet\Data\IncidentTemplate\CreateIncidentTemplateData;
use Cachet\Models\IncidentTemplate;

class CreateIncidentTemplate
{
    /**
     * Handle the action.
     */
    public function handle(CreateIncidentTemplateData $data): IncidentTemplate
    {
        return IncidentTemplate::create($data->toArray());
    }
}
