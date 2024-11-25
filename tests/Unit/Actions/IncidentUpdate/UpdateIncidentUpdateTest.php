<?php

use Cachet\Actions\Update\EditUpdate;
use Cachet\Models\Update;

it('can update an incident update', function () {
    $incidentUpdate = Update::factory()->forIncident()->create();

    $data = [
        'message' => 'Incident Updated',
    ];

    app(EditUpdate::class)->handle($incidentUpdate, $data);

    expect($incidentUpdate)
        ->message->toBe($data['message'])
        ->status->toBe($incidentUpdate->status);
});
