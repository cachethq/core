<?php

use Cachet\Events\Subscribers\SubscriberVerified;
use Cachet\Models\Component;
use Cachet\Models\Meta;
use Cachet\Models\Subscriber;
use Cachet\Notifications\VerifySubscriberEmail;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;

it('can verify', function () {
    Event::fake();

    $subscriber = Subscriber::factory()->create();
    $subscriber->verify();

    expect($subscriber)
        ->email_verified_at->toBeInstanceOf(DateTime::class);

    Event::assertDispatched(SubscriberVerified::class);
});

it('does not verify again', function () {
    Event::fake();

    $subscriber = Subscriber::factory()->verified()->create();
    $subscriber->verify();

    expect($subscriber)
        ->email_verified_at->toBeInstanceOf(DateTime::class);

    Event::assertNotDispatched(SubscriberVerified::class);
});

it('can reset the verification status', function () {
    $subscriber = Subscriber::factory()->verified()->create();
    $subscriber->resetVerification();

    expect($subscriber)
        ->email_verified_at->toBeNull()
        ->hasVerifiedEmail()->toBeFalse();
});

it('sends the verification notification', function () {
    Notification::fake();

    $subscriber = Subscriber::factory()->create();
    $subscriber->sendEmailVerificationNotification();

    Notification::assertSentTo($subscriber, VerifySubscriberEmail::class);
});

it('has components', function () {
    $subscriber = Subscriber::factory()->hasComponents()->create();

    expect($subscriber)
        ->components->toHaveCount(1)
        ->and($subscriber->components()->first())
        ->toBeInstanceOf(Component::class);
});

it('has meta', function () {
    $subscriber = Subscriber::factory()->withMeta()->create();

    expect($subscriber->metaValues())->toBe([
        'foo' => 'bar',
    ]);
});

it('purges meta when deleted', function () {
    $subscriber = Subscriber::factory()->withMeta()->create();

    $subscriber->delete();

    expect(Meta::query()->where('meta_id', $subscriber->id)->where('meta_type', 'subscriber')->count())->toBe(0);
});
