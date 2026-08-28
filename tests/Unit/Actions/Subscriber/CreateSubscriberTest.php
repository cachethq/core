<?php

use Cachet\Actions\Subscriber\CreateSubscriber;
use Cachet\Events\Subscribers\SubscriberCreated;
use Cachet\Events\Subscribers\SubscriberVerified;
use Cachet\Models\Component;
use Cachet\Models\Subscriber;
use Illuminate\Support\Facades\Event;

it('can create a subscriber', function () {
    Event::fake();

    $subscriber = app(CreateSubscriber::class)->handle('james@alt-three.com');

    expect($subscriber)
        ->email->toBe('james@alt-three.com')
        ->global->toBeTrue()
        ->email_verified_at->toBeNull()
        ->subscriptions->toBeEmpty();

    Event::assertDispatched(SubscriberCreated::class);
});

it('can create a non-global subscriber', function () {
    Event::fake();

    $subscriber = app(CreateSubscriber::class)->handle('james@alt-three.com', global: false);

    expect($subscriber)
        ->email->toBe('james@alt-three.com')
        ->global->toBeFalse()
        ->email_verified_at->toBeNull()
        ->subscriptions->toBeEmpty();

    Event::assertDispatched(SubscriberCreated::class);
});

it('can create a verified subscriber', function () {
    Event::fake();

    $subscriber = app(CreateSubscriber::class)->handle('james@alt-three.com', verified: true);

    expect($subscriber)
        ->email->toBe('james@alt-three.com')
        ->global->toBeTrue()
        ->email_verified_at->toBeInstanceOf(DateTime::class)
        ->subscriptions->toBeEmpty();

    Event::assertDispatched(SubscriberCreated::class);
});

it('can create a subscriber with components', function () {
    Event::fake();

    [$componentA, $componentB] = Component::factory()->count(2)->create();

    $subscriber = app(CreateSubscriber::class)->handle('james@alt-three.com', global: false, components: [
        $componentA->id, $componentB->id,
    ], verified: true);

    expect($subscriber)
        ->email->toBe('james@alt-three.com')
        ->global->toBeFalse()
        ->email_verified_at->toBeInstanceOf(DateTime::class)
        ->components->toHaveCount(2);

    Event::assertDispatched(SubscriberCreated::class);
});

it('updates the global state of an existing subscriber without resetting verification', function () {
    $existing = Subscriber::factory()->verified()->create([
        'email' => 'james@alt-three.com',
        'global' => true,
    ]);

    $subscriber = app(CreateSubscriber::class)->handle($existing->email, global: false);

    expect($subscriber->global)->toBeFalse();
    expect($subscriber->hasVerifiedEmail())->toBeTrue();
});

it('verifies an existing subscriber when requested', function () {
    $existing = Subscriber::factory()->create(['email' => 'james@alt-three.com']);

    Event::fake([SubscriberVerified::class]);

    $subscriber = app(CreateSubscriber::class)->handle($existing->email, verified: true);

    expect($subscriber->hasVerifiedEmail())->toBeTrue();
    Event::assertDispatched(SubscriberVerified::class);
});

it('does not duplicate an existing component subscription', function () {
    $component = Component::factory()->create();

    app(CreateSubscriber::class)->handle('james@alt-three.com', components: [$component->id]);
    $subscriber = app(CreateSubscriber::class)->handle('james@alt-three.com', components: [$component->id]);

    expect($subscriber->components)->toHaveCount(1);
    $this->assertDatabaseCount('subscriptions', 1);
});
