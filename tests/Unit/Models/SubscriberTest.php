<?php

use Cachet\Events\Subscribers\SubscriberVerified;
use Cachet\Models\Subscriber;
use Illuminate\Support\Facades\Event;

it('can verify', function () {
    Event::fake();

    $subscriber = Subscriber::factory()->create();
    $subscriber->verify();

    expect($subscriber)
        ->verified_at->toBeInstanceOf(DateTime::class);

    Event::assertDispatched(SubscriberVerified::class);
});

it('does not verify again', function () {
    Event::fake();

    $subscriber = Subscriber::factory()->verified()->create();
    $subscriber->verify();

    expect($subscriber)
        ->verified_at->toBeInstanceOf(DateTime::class);

    Event::assertNotDispatched(SubscriberVerified::class);
});

it('can reset the verification status', function () {
    $subscriber = Subscriber::factory()->verified()->create();
    $verifyCode = $subscriber->verify_code;
    $subscriber->resetVerification();

    expect($subscriber)
        ->verified_at->toBeNull()
        ->verify_code->not()->toBe($verifyCode);
});

it('has subscriptions', function () {
    $subscriber = Subscriber::factory()->hasSubscriptions()->create();

    expect($subscriber)
        ->subscriptions->toHaveCount(1)
        ->and($subscriber->subscriptions()->first())
        ->toMatchArray([
            'subscriber_id' => $subscriber->id,
        ]);
});
