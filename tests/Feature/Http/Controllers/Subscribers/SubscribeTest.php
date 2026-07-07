<?php

namespace Tests\Feature\Http\Controllers\Subscribers;

use Cachet\Events\Subscribers\SubscriberVerified;
use Cachet\Models\Subscriber;
use Cachet\Notifications\VerifySubscriberEmail;
use Cachet\Settings\MailSettings;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

use function Pest\Laravel\get;
use function Pest\Laravel\post;

beforeEach(function () {
    $settings = app(MailSettings::class);
    $settings->allow_subscribers = true;
    $settings->save();
});

it('shows the subscribe page', function () {
    get(route('cachet.subscribers.create'))
        ->assertOk()
        ->assertSee(__('cachet::subscriber.status_page.subscribe.heading'));
});

it('shows the subscribe button on the status page', function () {
    get(route('cachet.status-page'))
        ->assertOk()
        ->assertSee(route('cachet.subscribers.create'));
});

it('hides the subscribe page and button when subscriptions are not allowed', function () {
    $settings = app(MailSettings::class);
    $settings->allow_subscribers = false;
    $settings->save();

    get(route('cachet.status-page'))
        ->assertOk()
        ->assertDontSee(route('cachet.subscribers.create'));

    get(route('cachet.subscribers.create'))->assertNotFound();

    post(route('cachet.subscribers.store'), ['email' => 'james@example.com'])->assertNotFound();
});

it('subscribes and sends a verification email', function () {
    Notification::fake();

    post(route('cachet.subscribers.store'), ['email' => 'james@example.com'])
        ->assertRedirect(route('cachet.subscribers.create'))
        ->assertSessionHas('cachet_subscriber_status', 'subscribed');

    $subscriber = Subscriber::query()->where('email', 'james@example.com')->sole();

    expect($subscriber)
        ->hasVerifiedEmail()->toBeFalse()
        ->global->toBe(1);

    Notification::assertSentTo($subscriber, VerifySubscriberEmail::class);
});

it('resends the verification email for an existing unverified subscriber', function () {
    Notification::fake();

    $subscriber = Subscriber::factory()->create(['email' => 'james@example.com']);

    post(route('cachet.subscribers.store'), ['email' => 'james@example.com'])
        ->assertRedirect(route('cachet.subscribers.create'));

    expect(Subscriber::query()->where('email', 'james@example.com')->count())->toBe(1);

    Notification::assertSentTo($subscriber, VerifySubscriberEmail::class);
});

it('does not send a verification email to an already verified subscriber', function () {
    Notification::fake();

    Subscriber::factory()->verified()->create(['email' => 'james@example.com']);

    post(route('cachet.subscribers.store'), ['email' => 'james@example.com'])
        ->assertRedirect(route('cachet.subscribers.create'));

    Notification::assertNothingSent();
});

it('shows the confirmation state on the subscribe page after subscribing', function () {
    Notification::fake();

    $this->followingRedirects()
        ->post(route('cachet.subscribers.store'), ['email' => 'james@example.com'])
        ->assertOk()
        ->assertSee(__('cachet::subscriber.status_page.subscribe.subscribed_heading'))
        ->assertDontSee(__('cachet::subscriber.status_page.subscribe.consent'));
});

it('shows the verified state on the subscribe page after verifying', function () {
    $subscriber = Subscriber::factory()->create();

    $url = URL::temporarySignedRoute('cachet.subscribers.verify', now()->addMinutes(60), [
        'subscriber' => $subscriber->getKey(),
        'hash' => sha1($subscriber->email),
    ]);

    $this->followingRedirects()
        ->get($url)
        ->assertOk()
        ->assertSee(__('cachet::subscriber.status_page.subscribe.verified_heading'));
});

it('validates the email address', function () {
    post(route('cachet.subscribers.store'), ['email' => 'not-an-email'])
        ->assertSessionHasErrors('email');

    expect(Subscriber::query()->count())->toBe(0);
});

it('verifies a subscriber through the signed verification url', function () {
    Event::fake([SubscriberVerified::class]);

    $subscriber = Subscriber::factory()->create();

    $url = URL::temporarySignedRoute('cachet.subscribers.verify', now()->addMinutes(60), [
        'subscriber' => $subscriber->getKey(),
        'hash' => sha1($subscriber->email),
    ]);

    get($url)
        ->assertRedirect(route('cachet.subscribers.create'))
        ->assertSessionHas('cachet_subscriber_status', 'verified');

    expect($subscriber->fresh()->hasVerifiedEmail())->toBeTrue();

    Event::assertDispatched(SubscriberVerified::class);
});

it('completes the full subscribe and verify round trip', function () {
    Notification::fake();

    post(route('cachet.subscribers.store'), ['email' => 'james@example.com']);

    $subscriber = Subscriber::query()->where('email', 'james@example.com')->sole();

    $url = null;
    Notification::assertSentTo($subscriber, VerifySubscriberEmail::class, function (VerifySubscriberEmail $notification) use ($subscriber, &$url) {
        $url = $notification->toMail($subscriber)->viewData['verificationUrl'];

        return true;
    });

    get($url)->assertRedirect(route('cachet.subscribers.create'));

    expect($subscriber->fresh()->hasVerifiedEmail())->toBeTrue();
});

it('asks for confirmation before unsubscribing', function () {
    $subscriber = Subscriber::factory()->verified()->create();

    get($subscriber->unsubscribeUrl())
        ->assertOk()
        ->assertSee(__('cachet::subscriber.status_page.unsubscribe.heading'))
        ->assertSee($subscriber->email);

    expect($subscriber->fresh())->not->toBeNull();
});

it('unsubscribes a subscriber once confirmed', function () {
    $subscriber = Subscriber::factory()->verified()->create();

    $this->followingRedirects()
        ->post($subscriber->unsubscribeUrl())
        ->assertOk()
        ->assertSee(__('cachet::subscriber.status_page.subscribe.unsubscribed_heading'));

    expect($subscriber->fresh())->toBeNull();
});

it('rejects an unsigned unsubscribe confirmation', function () {
    $subscriber = Subscriber::factory()->verified()->create();

    post(route('cachet.subscribers.unsubscribe.destroy', [
        'subscriber' => $subscriber->getKey(),
        'hash' => sha1($subscriber->email),
    ]))->assertForbidden();

    expect($subscriber->fresh())->not->toBeNull();
});

it('rejects an unsigned unsubscribe url', function () {
    $subscriber = Subscriber::factory()->verified()->create();

    get(route('cachet.subscribers.unsubscribe', [
        'subscriber' => $subscriber->getKey(),
        'hash' => sha1($subscriber->email),
    ]))->assertForbidden();

    expect($subscriber->fresh())->not->toBeNull();
});

it('rejects an unsubscribe url with a mismatched hash', function () {
    $subscriber = Subscriber::factory()->verified()->create();

    $url = URL::signedRoute('cachet.subscribers.unsubscribe', [
        'subscriber' => $subscriber->getKey(),
        'hash' => sha1('someone-else@example.com'),
    ]);

    get($url)->assertForbidden();

    expect($subscriber->fresh())->not->toBeNull();
});

it('rejects an unsigned verification url', function () {
    $subscriber = Subscriber::factory()->create();

    get(route('cachet.subscribers.verify', [
        'subscriber' => $subscriber->getKey(),
        'hash' => sha1($subscriber->email),
    ]))->assertForbidden();

    expect($subscriber->fresh()->hasVerifiedEmail())->toBeFalse();
});

it('rejects a verification url with a mismatched hash', function () {
    $subscriber = Subscriber::factory()->create();

    $url = URL::temporarySignedRoute('cachet.subscribers.verify', now()->addMinutes(60), [
        'subscriber' => $subscriber->getKey(),
        'hash' => sha1('someone-else@example.com'),
    ]);

    get($url)->assertForbidden();

    expect($subscriber->fresh()->hasVerifiedEmail())->toBeFalse();
});
