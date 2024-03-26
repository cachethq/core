<?php

namespace Cachet\Actions\IncidentTemplate;

use Cachet\Models\IncidentTemplate;

class DeleteIncidentTemplate
{
    public function handle(IncidentTemplate $incidentTemplate)
    {
        $incidentTemplate->delete();
    }
}
