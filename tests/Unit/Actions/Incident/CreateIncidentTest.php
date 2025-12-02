<?php

use Cachet\Actions\Incident\CreateIncident;
use Cachet\Data\Requests\Incident\CreateIncidentRequestData;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Events\Incidents\IncidentCreated;
use Cachet\Models\Component;
use Cachet\Models\IncidentTemplate;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    Event::fake();
});

it('can create an incident', function () {
    $data = CreateIncidentRequestData::from([
        'name' => 'My Incident',
        'message' => 'This is an incident message.',
    ]);

    $incident = app(CreateIncident::class)->handle($data);

    expect($incident)
        ->name->toBe($data->name)
        ->message->toBe($data->message);

    Event::assertDispatched(IncidentCreated::class, fn ($event) => $event->incident->is($incident));
});

it('can create an incident with a given status', function () {
    $data = CreateIncidentRequestData::from([
        'name' => 'My Incident',
        'message' => 'This is an incident message',
        'status' => IncidentStatusEnum::investigating,
    ]);

    $incident = app(CreateIncident::class)->handle($data);

    expect($incident)
        ->name->toBe($data->name)
        ->message->toBe($data->message)
        ->status->toBe($data->status);

    Event::assertDispatched(IncidentCreated::class, fn ($event) => $event->incident->is($incident));
});

it('can create an incident with a twig template', function () {
    $template = IncidentTemplate::factory()->twig()->create([
        'slug' => 'my-template',
        'template' => 'This is a template: {{ incident.name }} foo: {{ foo }}',
    ]);

    $data = CreateIncidentRequestData::from([
        'name' => 'My Incident',
        'template' => 'my-template',
        'template_vars' => [
            'foo' => 'bar',
        ],
        'status' => IncidentStatusEnum::investigating,
    ]);

    $incident = app(CreateIncident::class)->handle($data);

    expect($incident)
        ->name->toBe($data->name)
        ->message->toBe('This is a template: My Incident foo: bar');

    Event::assertDispatched(IncidentCreated::class, fn ($event) => $event->incident->is($incident));
});

it('can create an incident with a blade template', function () {
    $template = IncidentTemplate::factory()->blade()->create([
        'slug' => 'my-template',
        'template' => 'This is a template: {{ $incident[\'name\'] }} foo: {{ $foo }}',
    ]);

    $data = CreateIncidentRequestData::from([
        'name' => 'My Incident',
        'template' => 'my-template',
        'template_vars' => [
            'foo' => 'bar',
        ],
        'status' => IncidentStatusEnum::investigating,
    ]);

    $incident = app(CreateIncident::class)->handle($data);

    expect($incident)
        ->name->toBe($data->name)
        ->message->toBe('This is a template: My Incident foo: bar');

    Event::assertDispatched(IncidentCreated::class, fn ($event) => $event->incident->is($incident));
});

it('attaches provided components to the incident', function () {
    [$componentA, $componentB] = Component::factory(2)->create();

    $data = CreateIncidentRequestData::from([
        'name' => 'My Incident',
        'message' => 'This is an incident message.',
        'components' => [
            ['id' => $componentA->id, 'status' => ComponentStatusEnum::performance_issues->value],
            ['id' => $componentB->id, 'status' => ComponentStatusEnum::partial_outage->value],
        ],
    ]);

    $incident = app(CreateIncident::class)->handle($data);

    expect($incident->components)->toHaveCount(2)
        ->and($incident->components->pluck('pivot.component_status')->all())
        ->toContain(ComponentStatusEnum::performance_issues)
        ->toContain(ComponentStatusEnum::partial_outage);
});
