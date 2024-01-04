<?php

use Cachet\Actions\IncidentUpdate\UpdateIncidentUpdate;
use Cachet\Models\IncidentUpdate;

it('can update an incident update', function () {
    $incidentUpdate = IncidentUpdate::factory()->create();

    $data = [
        'message' => 'Incident Updated',
    ];

    app(UpdateIncidentUpdate::class)->handle($incidentUpdate, $data);

    expect($incidentUpdate)
        ->message->toBe($data['message'])
        ->status->toBe($incidentUpdate->status);
});
