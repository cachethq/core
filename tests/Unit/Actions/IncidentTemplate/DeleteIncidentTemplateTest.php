<?php

use Cachet\Actions\IncidentTemplate\DeleteIncidentTemplate;
use Cachet\Models\IncidentTemplate;

it('can delete an incident template', function () {
    $incidentTemplate = IncidentTemplate::factory()->create();

    DeleteIncidentTemplate::run($incidentTemplate);

    expect($incidentTemplate->fresh())->toBeNull();
});
