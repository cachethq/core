<?php

use Cachet\Actions\Subscriber\UnsubscribeSubscriber;
use Cachet\Events\Subscribers\SubscriberUnsubscribed;
use Cachet\Models\Subscriber;
use Illuminate\Support\Facades\Event;

it('can unsubscribe a subscriber', function () {
    Event::fake();

    $subscriber = Subscriber::factory()->create();

    UnsubscribeSubscriber::run($subscriber);

    expect($subscriber->fresh())
        ->toBeNull();

    Event::assertDispatched(SubscriberUnsubscribed::class);
});
