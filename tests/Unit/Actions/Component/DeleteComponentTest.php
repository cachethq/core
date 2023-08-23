<?php

use Cachet\Actions\Component\DeleteComponent;
use Cachet\Events\Components\ComponentDeleted;
use Cachet\Models\Component;
use Illuminate\Support\Facades\Event;

it('can delete components', function () {
    Event::fake();

    $component = Component::factory()->create();

    DeleteComponent::run($component);

    expect(Component::find($component->id))->toBeNull();

    Event::assertDispatched(ComponentDeleted::class, fn ($event) => $event->component->is($component));
});
