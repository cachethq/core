<?php

use Cachet\Actions\IncidentUpdate\DeleteIncidentUpdate;
use Cachet\Models\IncidentUpdate;

it('can delete an incident update', function () {
    $incidentUpdate = IncidentUpdate::factory()->forIncident()->create();

    DeleteIncidentUpdate::run($incidentUpdate);

    $this->assertDatabaseMissing('incident_updates', [
        'id' => $incidentUpdate->id,
    ]);
});
