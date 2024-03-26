<?php

use Cachet\Actions\Subscriber\CreateSubscriber;
use Cachet\Events\Subscribers\SubscriberCreated;
use Cachet\Models\Component;
use Illuminate\Support\Facades\Event;

it('can create a subscriber', function () {
    Event::fake();

    $subscriber = app(CreateSubscriber::class)->handle('james@alt-three.com');

    expect($subscriber)
        ->email->toBe('james@alt-three.com')
        ->global->toBeFalse()
        ->verified_at->toBeNull()
        ->subscriptions->toBeEmpty();

    Event::assertDispatched(SubscriberCreated::class);
});

it('can create a global subscriber', function () {
    Event::fake();

    $subscriber = app(CreateSubscriber::class)->handle('james@alt-three.com', global: true);

    expect($subscriber)
        ->email->toBe('james@alt-three.com')
        ->global->toBeTrue()
        ->verified_at->toBeNull()
        ->subscriptions->toBeEmpty();

    Event::assertDispatched(SubscriberCreated::class);
});

it('can create a verified subscriber', function () {
    Event::fake();

    $subscriber = app(CreateSubscriber::class)->handle('james@alt-three.com', verified: true);

    expect($subscriber)
        ->email->toBe('james@alt-three.com')
        ->global->toBeFalse()
        ->verified_at->toBeInstanceOf(DateTime::class)
        ->subscriptions->toBeEmpty();

    Event::assertDispatched(SubscriberCreated::class);
});

it('can create a subscriber with components', function () {
    Event::fake();

    [$componentA, $componentB] = Component::factory()->count(2)->create();

    $subscriber = app(CreateSubscriber::class)->handle('james@alt-three.com', components: [
        $componentA->id, $componentB->id,
    ], verified: true);

    expect($subscriber)
        ->email->toBe('james@alt-three.com')
        ->global->toBeFalse()
        ->verified_at->toBeInstanceOf(DateTime::class)
        ->components->toHaveCount(2);

    Event::assertDispatched(SubscriberCreated::class);
});
