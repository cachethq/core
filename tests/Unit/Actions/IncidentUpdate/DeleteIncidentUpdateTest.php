<?php

use Cachet\Actions\Update\DeleteUpdate;
use Cachet\Models\IncidentUpdate;

it('can delete an incident update', function () {
    $incidentUpdate = IncidentUpdate::factory()->forIncident()->create();

    app(DeleteUpdate::class)->handle($incidentUpdate);

    $this->assertDatabaseMissing('incident_updates', [
        'id' => $incidentUpdate->id,
    ]);
});
