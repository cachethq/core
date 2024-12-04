<?php

use Cachet\Actions\Update\EditUpdate;
use Cachet\Data\IncidentUpdate\EditIncidentUpdateData;
use Cachet\Data\ScheduleUpdate\EditScheduleUpdateData;
use Cachet\Models\Update;

it('can update an incident update', function () {
    $update = Update::factory()->forIncident()->create();

    $data = EditIncidentUpdateData::from([
        'message' => 'Incident Updated',
    ]);

    app(EditUpdate::class)->handle($update, $data);

    expect($update)
        ->message->toBe($data->message)
        ->status->toBe($update->status);
});

it('can update a schedule update', function () {
    $update = Update::factory()->forSchedule()->create();

    $data = EditScheduleUpdateData::from([
        'message' => 'Schedule Updated',
    ]);

    app(EditUpdate::class)->handle($update, $data);

    expect($update)
        ->message->toBe($data->message)
        ->status->toBe($update->status);
});
