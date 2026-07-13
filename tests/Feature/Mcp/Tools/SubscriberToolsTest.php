<?php

use Cachet\Mcp\CachetServer;
use Cachet\Mcp\Tools\Subscribers\CreateSubscriber;
use Cachet\Mcp\Tools\Subscribers\ListSubscribers;
use Cachet\Mcp\Tools\Subscribers\UnsubscribeSubscriber;
use Cachet\Mcp\Tools\Subscribers\UpdateSubscriber;
use Cachet\Models\Subscriber;
use Cachet\Notifications\VerifySubscriberEmail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Workbench\App\User;

it('hides the subscribers list from guests', function () {
    Subscriber::factory(2)->create();

    CachetServer::tool(ListSubscribers::class)
        ->assertHasErrors();
});

it('hides the subscribers list from tokens without the ability', function () {
    Sanctum::actingAs(User::factory()->create());

    Subscriber::factory(2)->create();

    CachetServer::tool(ListSubscribers::class)
        ->assertHasErrors();
});

it('lists subscribers with the ability', function () {
    Sanctum::actingAs(User::factory()->create(), ['subscribers.manage']);

    Subscriber::factory(2)->create();

    CachetServer::tool(ListSubscribers::class)
        ->assertOk()
        ->assertStructuredContent(fn (AssertableJson $json) => $json->has('data', 2)->etc());
});

it('creates a subscriber and sends a verification email', function () {
    Notification::fake();

    Sanctum::actingAs(User::factory()->create(), ['subscribers.manage']);

    CachetServer::tool(CreateSubscriber::class, ['email' => 'james@example.com'])
        ->assertOk()
        ->assertSee('james@example.com');

    $subscriber = Subscriber::query()->firstWhere('email', 'james@example.com');

    expect($subscriber)->not->toBeNull();

    Notification::assertSentTo($subscriber, VerifySubscriberEmail::class);
});

it('creates a verified subscriber without sending a verification email', function () {
    Notification::fake();

    Sanctum::actingAs(User::factory()->create(), ['subscribers.manage']);

    CachetServer::tool(CreateSubscriber::class, [
        'email' => 'james@example.com',
        'verified' => true,
    ])->assertOk();

    Notification::assertNothingSent();
});

it('validates the subscriber email', function () {
    Sanctum::actingAs(User::factory()->create(), ['subscribers.manage']);

    CachetServer::tool(CreateSubscriber::class, ['email' => 'not-an-email'])
        ->assertHasErrors();
});

it('updates a subscriber', function () {
    Sanctum::actingAs(User::factory()->create(), ['subscribers.manage']);

    $subscriber = Subscriber::factory()->create(['email' => 'old@example.com']);

    CachetServer::tool(UpdateSubscriber::class, [
        'id' => $subscriber->id,
        'email' => 'new@example.com',
    ])->assertOk();

    expect($subscriber->fresh()->email)->toBe('new@example.com');
});

it('unsubscribes a subscriber', function () {
    Sanctum::actingAs(User::factory()->create(), ['subscribers.delete']);

    $subscriber = Subscriber::factory()->create();

    CachetServer::tool(UnsubscribeSubscriber::class, ['id' => $subscriber->id])
        ->assertOk();

    expect(Subscriber::query()->find($subscriber->id))->toBeNull();
});
