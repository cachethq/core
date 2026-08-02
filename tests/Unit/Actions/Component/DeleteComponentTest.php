<?php

use Cachet\Actions\Component\DeleteComponent;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Events\Components\ComponentDeleted;
use Cachet\Models\Component;
use Cachet\Models\Incident;
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

it('keeps subscriptions when the component is soft deleted', function () {
    $component = Component::factory()->hasSubscribers(1, ['email' => 'james@alt-three.com'])->create();
    $subscriber = $component->subscribers()->first();

    app(DeleteComponent::class)->handle($component);

    $this->assertSoftDeleted('components', [
        'id' => $component->id,
    ]);
    $this->assertDatabaseHas('subscriptions', [
        'subscriber_id' => $subscriber->id,
    ]);
});

it('restores a component with its subscriptions intact', function () {
    $component = Component::factory()->hasSubscribers(1, ['email' => 'james@alt-three.com'])->create();

    app(DeleteComponent::class)->handle($component);

    $component->restore();

    expect($component->subscribers()->count())->toBe(1);
});

it('purges subscriptions and pivot rows when the component is hard deleted', function () {
    $component = Component::factory()->hasSubscribers(1, ['email' => 'james@alt-three.com'])->create();
    $subscriber = $component->subscribers()->first();

    $incident = Incident::factory()->create();
    $incident->components()->attach($component->id, ['component_status' => ComponentStatusEnum::major_outage]);

    $component->forceDelete();

    $this->assertDatabaseMissing('subscriptions', [
        'subscriber_id' => $subscriber->id,
    ]);
    $this->assertDatabaseMissing('incident_components', [
        'component_id' => $component->id,
    ]);
});
