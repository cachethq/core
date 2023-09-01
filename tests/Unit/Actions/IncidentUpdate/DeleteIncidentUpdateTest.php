<?php

use Cachet\Actions\IncidentUpdate\DeleteIncidentUpdate;
use Cachet\Models\IncidentUpdate;

it('can delete an incident update', function () {
    $incidentUpdate = IncidentUpdate::factory()->forIncident()->create();

    DeleteIncidentUpdate::run($incidentUpdate);

    expect(IncidentUpdate::find($incidentUpdate->id))->toBeNull();
});
