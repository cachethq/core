<?php

use Cachet\Actions\Incident\UpdateIncident;
use Cachet\Models\Incident;

it('can update an incident', function () {
    $incident = Incident::factory()->create();

    $data = [
        'name' => 'Incident Updated',
    ];

    UpdateIncident::run($incident, $data);

    expect($incident)
        ->name->toBe($data['name']);
});
