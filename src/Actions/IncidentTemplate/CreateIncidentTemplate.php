<?php

namespace Cachet\Actions\IncidentTemplate;

use Cachet\Data\Requests\IncidentTemplate\CreateIncidentTemplateRequestData;
use Cachet\Models\IncidentTemplate;

class CreateIncidentTemplate
{
    /**
     * Handle the action.
     */
    public function handle(CreateIncidentTemplateRequestData $data): IncidentTemplate
    {
        return IncidentTemplate::create($data->toArray());
    }
}
