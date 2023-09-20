<?php

use Cachet\Actions\Incident\CreateIncident;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Events\Incidents\IncidentCreated;
use Cachet\Models\IncidentTemplate;
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
        'status' => IncidentStatusEnum::investigating,
    ];

    $incident = CreateIncident::run($data);

    expect($incident)
        ->name->toBe($data['name'])
        ->message->toBe($data['message'])
        ->status->toBe($data['status']);

    Event::assertDispatched(IncidentCreated::class, fn ($event) => $event->incident->is($incident));
});

it('can create an incident with a twig template', function () {
    $template = IncidentTemplate::factory()->twig()->create([
        'slug' => 'my-template',
        'template' => 'This is a template: {{ incident.name }} foo: {{ foo }}',
    ]);

    $data = [
        'name' => 'My Incident',
        'template' => 'my-template',
        'template_vars' => [
            'foo' => 'bar',
        ],
        'status' => IncidentStatusEnum::investigating,
    ];

    $incident = CreateIncident::run($data);

    expect($incident)
        ->name->toBe($data['name'])
        ->message->toBe('This is a template: My Incident foo: bar');

    Event::assertDispatched(IncidentCreated::class, fn ($event) => $event->incident->is($incident));
});

it('can create an incident with a blade template', function () {
    $template = IncidentTemplate::factory()->blade()->create([
        'slug' => 'my-template',
        'template' => 'This is a template: {{ $incident[\'name\'] }} foo: {{ $foo }}',
    ]);

    $data = [
        'name' => 'My Incident',
        'template' => 'my-template',
        'template_vars' => [
            'foo' => 'bar',
        ],
        'status' => IncidentStatusEnum::investigating,
    ];

    $incident = CreateIncident::run($data);

    expect($incident)
        ->name->toBe($data['name'])
        ->message->toBe('This is a template: My Incident foo: bar');

    Event::assertDispatched(IncidentCreated::class, fn ($event) => $event->incident->is($incident));
});
