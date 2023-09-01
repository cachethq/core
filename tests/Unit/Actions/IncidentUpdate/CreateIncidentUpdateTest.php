<?php

use Cachet\Actions\IncidentUpdate\CreateIncidentUpdate;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Models\Incident;

it('can create an incident update', function () {
    $incident = Incident::factory()->create();

    $data = [
        'message' => 'This is an update message.',
        'status' => IncidentStatusEnum::investigating,
    ];

    $incidentUpdate = CreateIncidentUpdate::run($incident, $data);

    expect($incidentUpdate)
        ->message->toBe($data['message']);
})->todo('Need to figure out the user_id issue.');
