<?php

use Cachet\Actions\Component\UpdateComponent;
use Cachet\Data\Requests\Component\UpdateComponentRequestData;
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

    $data = UpdateComponentRequestData::from([
        'name' => 'My Updated Component',
        'description' => 'My updated component description.',
    ]);

    app(UpdateComponent::class)->handle(
        $component,
        $data
    );

    expect($component)
        ->name->toBe($data->name)
        ->description->toBe($data->description);

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

    app(UpdateComponent::class)->handle($component, UpdateComponentRequestData::from([
        'status' => ComponentStatusEnum::major_outage,
    ]));

    Event::assertDispatched(ComponentStatusWasChanged::class, function (ComponentStatusWasChanged $event) use ($component) {
        return $event->component->is($component) &&
            $event->oldStatus === ComponentStatusEnum::operational &&
            $event->newStatus === ComponentStatusEnum::major_outage;
    });
});
