<?php

use Cachet\Enums\WebhookEventEnum;
use Cachet\Listeners\WebhookCallEventListener;
use Cachet\Models\WebhookAttempt;
use Cachet\Models\WebhookSubscription;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\TransferStats;
use Spatie\WebhookServer\Events\WebhookCallFailedEvent;
use Spatie\WebhookServer\Events\WebhookCallSucceededEvent;

function makeWebhookEvent(string $eventClass, WebhookSubscription $subscription, ?Response $response = null, ?TransferStats $stats = null, int $attempt = 1): object
{
    return new $eventClass(
        httpVerb: 'POST',
        webhookUrl: $subscription->url,
        payload: ['event' => WebhookEventEnum::component_created->value, 'body' => []],
        headers: [],
        meta: [
            'subscription_id' => $subscription->id,
            'event' => WebhookEventEnum::component_created->value,
        ],
        tags: [],
        attempt: $attempt,
        response: $response,
        errorType: null,
        errorMessage: null,
        uuid: 'test-uuid',
        transferStats: $stats,
    );
}

it('records a successful webhook attempt', function () {
    $subscription = WebhookSubscription::factory()->create();

    $event = makeWebhookEvent(
        WebhookCallSucceededEvent::class,
        $subscription,
        response: new Response(200),
        stats: new TransferStats(
            new GuzzleHttp\Psr7\Request('POST', $subscription->url),
            new Response(200),
            0.42,
        ),
    );

    app(WebhookCallEventListener::class)->handle($event);

    $attempt = WebhookAttempt::query()->firstOrFail();

    expect($attempt)
        ->subscription_id->toBe($subscription->id)
        ->event->toBe(WebhookEventEnum::component_created)
        ->attempt->toBe(1)
        ->response_code->toBe(200)
        ->transfer_time->toEqual(0.42);

    expect(json_decode($attempt->payload, true))
        ->toBe(['event' => WebhookEventEnum::component_created->value, 'body' => []]);
});

it('records a failed webhook attempt without response or transfer stats', function () {
    $subscription = WebhookSubscription::factory()->create();

    $event = makeWebhookEvent(
        WebhookCallFailedEvent::class,
        $subscription,
        attempt: 3,
    );

    app(WebhookCallEventListener::class)->handle($event);

    $attempt = WebhookAttempt::query()->firstOrFail();

    expect($attempt)
        ->subscription_id->toBe($subscription->id)
        ->attempt->toBe(3)
        ->response_code->toBeNull()
        ->transfer_time->toBeNull();
});

it('recalculates the subscription success rate after recording an attempt', function () {
    $subscription = WebhookSubscription::factory()->create(['success_rate_24h' => 0]);

    $event = makeWebhookEvent(
        WebhookCallSucceededEvent::class,
        $subscription,
        response: new Response(204),
    );

    app(WebhookCallEventListener::class)->handle($event);

    expect($subscription->fresh()->success_rate_24h)->toBe('100.00%');
});
