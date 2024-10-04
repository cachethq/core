<?php

namespace Cachet\Actions\IncidentTemplate;

use Cachet\Models\IncidentTemplate;

class DeleteIncidentTemplate
{
    /**
     * Handle the action.
     */
    public function handle(IncidentTemplate $incidentTemplate): void
    {
        $incidentTemplate->delete();
    }
}
