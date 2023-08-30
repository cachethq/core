<?php

use Cachet\Actions\Incident\CreateIncident;
use Cachet\Data\IncidentData;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Events\Incidents\IncidentCreated;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    Event::fake();
});

it('can create an incident', function () {
    $data = [
        'name' => 'My Incident',
        'message' => 'This is an incident message.',
    ];

    $incident = CreateIncident::run($data);

    expect($incident)
        ->name->toBe($data['name'])
        ->message->toBe($data['message']);

    Event::assertDispatched(IncidentCreated::class, fn ($event) => $event->incident->is($incident));
});

it('can create an incident with a given status', function () {
    $data = [
        'name' => 'My Incident',
        'message' => 'This is an incident message',
        'status' => IncidentStatusEnum::investigating
    ];

    $incident = CreateIncident::run($data);

    expect($incident)
        ->name->toBe($data['name'])
        ->message->toBe($data['message'])
        ->status->toBe($data['status']);

    Event::assertDispatched(IncidentCreated::class, fn ($event) => $event->incident->is($incident));
});
