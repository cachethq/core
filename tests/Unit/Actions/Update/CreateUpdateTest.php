<?php

use Cachet\Actions\Update\CreateUpdate;
use Cachet\Data\IncidentUpdate\CreateIncidentUpdateData;
use Cachet\Data\ScheduleUpdate\CreateScheduleUpdateData;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Models\Incident;
use Cachet\Models\Schedule;

it('can create an incident update', function () {
    $incident = Incident::factory()->create();

    $data = CreateIncidentUpdateData::from([
        'message' => 'This is an update message.',
        'status' => IncidentStatusEnum::investigating,
    ]);

    $incidentUpdate = app(CreateUpdate::class)->handle($incident, $data);

    expect($incidentUpdate)
        ->message->toBe($data->message);
});

it('an incident\'s computed latest status equals the new status', function () {
    $incident = Incident::factory()->create([
        'status' => IncidentStatusEnum::investigating,
    ]);

    $data = CreateIncidentUpdateData::from([
        'message' => 'This is an update message.',
        'status' => IncidentStatusEnum::identified,
    ]);

    $incidentUpdate = app(CreateUpdate::class)->handle($incident, $data);

    expect($incidentUpdate)
        ->message->toBe($data->message)
        ->and($incident->fresh())
        ->latestStatus->toEqual(IncidentStatusEnum::identified);
});

it('can create a schedule update', function () {
    $schedule = Schedule::factory()->create();

    $data = CreateScheduleUpdateData::from([
        'message' => 'This is an update message for a schedule.',
        'status' => IncidentStatusEnum::investigating,
    ]);

    $incidentUpdate = app(CreateUpdate::class)->handle($schedule, $data);

    expect($incidentUpdate)
        ->message->toBe($data->message);
});
