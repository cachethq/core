<?php

use Cachet\Actions\Component\UpdateComponent;
use Cachet\Data\ComponentData;
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
});
