<?php

use Cachet\Actions\Incident\SyncIncidentStatus;
use Cachet\Actions\Update\EditUpdate;
use Cachet\Data\Requests\IncidentUpdate\EditIncidentUpdateRequestData;
use Cachet\Data\Requests\ScheduleUpdate\EditScheduleUpdateRequestData;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Models\Incident;
use Cachet\Models\Update;

it('can update an incident update', function () {
    $update = Update::factory()->forIncident()->create();

    $data = EditIncidentUpdateRequestData::from([
        'message' => 'Incident Updated',
    ]);

    app(EditUpdate::class)->handle($update, $data);

    expect($update)
        ->message->toBe($data->message)
        ->status->toBe($update->status);
});

it('can update a schedule update', function () {
    $update = Update::factory()->forSchedule()->create();

    $data = EditScheduleUpdateRequestData::from([
        'message' => 'Schedule Updated',
    ]);

    app(EditUpdate::class)->handle($update, $data);

    expect($update)
        ->message->toBe($data->message)
        ->status->toBe($update->status);
});

it('restores the incident baseline status when the last status-bearing update is cleared', function () {
    $incident = Incident::factory()->create([
        'status' => IncidentStatusEnum::investigating,
        'baseline_status' => IncidentStatusEnum::investigating,
    ]);

    $update = $incident->updates()->create([
        'status' => IncidentStatusEnum::identified,
        'message' => 'We found the issue.',
    ]);

    app(SyncIncidentStatus::class)->handle($incident);

    expect($incident->fresh()->status)->toBe(IncidentStatusEnum::identified);

    app(EditUpdate::class)->handle($update, EditIncidentUpdateRequestData::from([
        'status' => null,
    ]));

    expect($incident->fresh()->status)->toBe(IncidentStatusEnum::investigating);
});
