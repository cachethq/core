<?php

namespace Cachet\Actions\IncidentTemplate;

use Cachet\Models\IncidentTemplate;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteIncidentTemplate
{
    use AsAction;

    public function handle(IncidentTemplate $incidentTemplate)
    {
        $incidentTemplate->delete();
    }
}
