<?php

use Cachet\Actions\Component\DeleteComponent;
use Cachet\Events\Components\ComponentDeleted;
use Cachet\Models\Component;
use Illuminate\Support\Facades\Event;

it('can delete components', function () {
    Event::fake();

    $component = Component::factory()->create();

    app(DeleteComponent::class)->handle($component);

    $this->assertSoftDeleted('components', [
        'id' => $component->id,
    ]);

    Event::assertDispatched(ComponentDeleted::class, fn ($event) => $event->component->is($component));
});

it('deletes attached subscriptions when deleted', function () {
    $component = Component::factory()->hasSubscribers(1, ['email' => 'james@alt-three.com'])->create();
    $subscriber = $component->subscribers()->first();

    $this->assertDatabaseHas('subscriptions', [
        'subscriber_id' => $subscriber->id,
    ]);

    app(DeleteComponent::class)->handle($component);

    $this->assertDatabaseMissing('subscriptions', [
        'subscriber_id' => $subscriber->id,
    ]);
});
