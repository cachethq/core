<?php

use Cachet\Actions\Component\UpdateComponent;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Events\Components\ComponentStatusWasChanged;
use Cachet\Events\Components\ComponentUpdated;
use Cachet\Models\Component;
use Illuminate\Support\Facades\Event;

it('can update a component', function () {
    Event::fake();

    $component = Component::factory()->create([
        'name' => 'My Component',
        'description' => 'My component description.',
    ]);

    $data = [
        'name' => 'My Updated Component',
        'description' => 'My updated component description.',
    ];

    UpdateComponent::run(
        $component,
        $data
    );

    expect($component)
        ->name->toBe($data['name'])
        ->description->toBe($data['description']);

    Event::assertDispatched(ComponentUpdated::class, fn ($event) => $event->component->is($component));
    Event::assertNotDispatched(ComponentStatusWasChanged::class);
});

it('dispatches ComponentStatusWasChanged when the status is changed', function () {
    Event::fake();

    $component = Component::factory()->create([
        'name' => 'My Component',
        'description' => 'My component description.',
        'status' => ComponentStatusEnum::operational,
    ]);

    UpdateComponent::run($component, [
        'status' => ComponentStatusEnum::major_outage,
    ]);

    Event::assertDispatched(ComponentStatusWasChanged::class, function (ComponentStatusWasChanged $event) use ($component) {
        return $event->component->is($component) &&
            $event->oldStatus === ComponentStatusEnum::operational &&
            $event->newStatus === ComponentStatusEnum::major_outage;
    });
});
