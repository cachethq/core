<?php

use Cachet\Actions\Subscriber\UnsubscribeSubscriber;
use Cachet\Events\Subscribers\SubscriberUnsubscribed;
use Cachet\Models\Component;
use Cachet\Models\Subscriber;
use Illuminate\Support\Facades\Event;

it('can unsubscribe a subscriber', function () {
    Event::fake();

    $subscriber = Subscriber::factory()->create();

    app(UnsubscribeSubscriber::class)->handle($subscriber);

    expect($subscriber->fresh())
        ->toBeNull();

    Event::assertDispatched(SubscriberUnsubscribed::class);
});

it('detaches component subscriptions without deleting the components', function () {
    $subscriber = Subscriber::factory()->hasComponents()->create();
    $component = $subscriber->components->sole();

    app(UnsubscribeSubscriber::class)->handle($subscriber);

    expect($subscriber->fresh())->toBeNull()
        ->and(Component::query()->find($component->id))->not->toBeNull();
});
