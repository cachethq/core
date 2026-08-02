<?php

use Cachet\Actions\Incident\UpdateIncident;
use Cachet\Data\Requests\Incident\UpdateIncidentRequestData;
use Cachet\Events\Incidents\IncidentUpdated;
use Cachet\Models\Incident;
use Cachet\Models\Meta;

it('can update an incident', function () {
    $incident = Incident::factory()->create();

    $data = UpdateIncidentRequestData::from([
        'name' => 'Incident Updated',
    ]);

    app(UpdateIncident::class)->handle($incident, $data);

    expect($incident)
        ->name->toBe($data->name);
});

it('dispatches the IncidentUpdated event exactly once', function () {
    Event::fake();

    $incident = Incident::factory()->create();

    app(UpdateIncident::class)->handle($incident, UpdateIncidentRequestData::from([
        'name' => 'New Incident Title',
    ]));

    Event::assertDispatched(IncidentUpdated::class, fn (IncidentUpdated $event) => $event->incident->is($incident));
    Event::assertDispatchedTimes(IncidentUpdated::class, 1);
});

it('rolls the whole update back when a write fails part-way', function () {
    $incident = Incident::factory()->create(['name' => 'Original Name']);

    Meta::creating(fn () => throw new RuntimeException('boom'));

    try {
        app(UpdateIncident::class)->handle($incident, UpdateIncidentRequestData::from([
            'name' => 'Updated Name',
            'meta' => ['cluster' => 'eu-west'],
        ]));
    } catch (RuntimeException) {
        //
    }

    expect($incident->fresh()->name)->toBe('Original Name');
});
