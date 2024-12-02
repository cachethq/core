<?php

use Cachet\Actions\Update\EditUpdate;
use Cachet\Models\Update;

it('can update an incident update', function () {
    $update = Update::factory()->forIncident()->create();

    $data = [
        'message' => 'Incident Updated',
    ];

    app(EditUpdate::class)->handle($update, $data);

    expect($update)
        ->message->toBe($data['message'])
        ->status->toBe($update->status);
});

it('can update a schedule update', function () {
    $update = Update::factory()->forSchedule()->create();

    $data = [
        'message' => 'Schedule Updated',
    ];

    app(EditUpdate::class)->handle($update, $data);

    expect($update)
        ->message->toBe($data['message'])
        ->status->toBe($update->status);
});
