<?php

use Cachet\Actions\Component\CreateComponent;
use Cachet\Data\ComponentData;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Events\Components\ComponentCreated;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    Event::fake();
});

it('can create a component', function () {
    $data = [
        'name' => 'My Component',
        'description' => 'My component description',
    ];

    $component = CreateComponent::run($data);

    expect($component)
        ->name->toBe($data['name'])
        ->description->toBe($data['description']);

    Event::assertDispatched(ComponentCreated::class, fn ($event) => $event->component->is($component));
});

it('can create a component with a given status', function () {
    $data = [
        'name' => 'My Component',
        'description' => 'My component description',
        'status' => ComponentStatusEnum::performance_issues,
    ];

    $component = CreateComponent::run($data);

    expect($component)
        ->name->toBe($data['name'])
        ->description->toBe($data['description'])
        ->status->toBe($data['status']);

    Event::assertDispatched(ComponentCreated::class, fn ($event) => $event->component->is($component));
});
