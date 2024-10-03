<?php

use Cachet\Actions\Incident\UpdateIncident;
use Cachet\Models\Incident;

it('can update an incident', function () {
    $incident = Incident::factory()->create();

    $data = [
        'name' => 'Incident Updated',
    ];

    app(UpdateIncident::class)->handle($incident, $data);

    expect($incident)
        ->name->toBe($data['name']);
});

it('dispatches the IncidentUpdated event', function () {
    Event::fake();

    $incident = Incident::factory()->create();

    app(UpdateIncident::class)->handle($incident, [
        'name' => 'New Incident Title',
    ]);

    Event::assertDispatched(IncidentUpdated::class, function (IncidentUpdated $event) use ($incident) {
        return $event->incident->is($incident);
    });
});
