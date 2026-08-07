<?php

use Cachet\Actions\Incident\UpdateIncident;
use Cachet\Data\Requests\Incident\UpdateIncidentRequestData;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Events\Incidents\IncidentUpdated;
use Cachet\Models\Incident;

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

it('updates the incident baseline status when the status changes directly', function () {
    $incident = Incident::factory()->create([
        'status' => IncidentStatusEnum::investigating,
        'baseline_status' => IncidentStatusEnum::investigating,
    ]);

    app(UpdateIncident::class)->handle($incident, UpdateIncidentRequestData::from([
        'status' => IncidentStatusEnum::identified,
    ]));

    expect($incident->fresh())
        ->status->toBe(IncidentStatusEnum::identified)
        ->baseline_status->toBe(IncidentStatusEnum::identified);
});
