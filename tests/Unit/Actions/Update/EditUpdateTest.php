<?php

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

it('rolls the edit back when the incident status sync fails', function () {
    $incident = Incident::factory()->create(['status' => IncidentStatusEnum::investigating]);
    $update = Update::factory()->forIncident($incident)->create([
        'status' => IncidentStatusEnum::investigating,
        'message' => 'Original message.',
    ]);

    Incident::updated(fn () => throw new RuntimeException('boom'));

    try {
        app(EditUpdate::class)->handle($update, EditIncidentUpdateRequestData::from([
            'status' => IncidentStatusEnum::fixed,
            'message' => 'Updated message.',
        ]));
    } catch (RuntimeException) {
        //
    }

    expect($update->fresh())
        ->message->toBe('Original message.')
        ->status->toBe(IncidentStatusEnum::investigating);
});
