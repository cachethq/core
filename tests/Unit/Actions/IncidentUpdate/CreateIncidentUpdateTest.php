<?php

use Cachet\Actions\Update\CreateUpdate;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Models\Incident;

it('can create an incident update', function () {
    $incident = Incident::factory()->create();

    $data = [
        'message' => 'This is an update message.',
        'status' => IncidentStatusEnum::investigating,
    ];

    $incidentUpdate = app(CreateUpdate::class)->handle($incident, $data);

    expect($incidentUpdate)
        ->message->toBe($data['message']);
});

it('updates the incident when the status is changed', function () {
    $incident = Incident::factory()->create([
        'status' => IncidentStatusEnum::investigating,
    ]);

    $data = [
        'message' => 'This is an update message.',
        'status' => IncidentStatusEnum::identified,
    ];

    $incidentUpdate = app(CreateUpdate::class)->handle($incident, $data);

    expect($incidentUpdate)
        ->message->toBe($data['message'])
        ->and($incident->fresh())
        ->status->toEqual(IncidentStatusEnum::identified);
});
