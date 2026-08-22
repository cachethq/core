<?php

use Cachet\Actions\Webhook\DispatchWebhooks;
use Cachet\Enums\WebhookEventEnum;
use Cachet\Events\Components\ComponentCreated;
use Cachet\Models\Component;
use Cachet\Models\WebhookSubscription;
use Illuminate\Support\Facades\Queue;
use Spatie\WebhookServer\CallWebhookJob;

beforeEach(function () {
    Queue::fake();
});

it('dispatches matching webhook subscriptions with the event payload', function () {
    $component = Component::factory()->create();

    $matching = WebhookSubscription::factory()
        ->selectedEvents([WebhookEventEnum::component_created])
        ->create();

    WebhookSubscription::factory()
        ->selectedEvents([WebhookEventEnum::component_updated])
        ->create();

    app(DispatchWebhooks::class)->handle(new ComponentCreated($component));

    Queue::assertPushed(CallWebhookJob::class, function (CallWebhookJob $job) use ($component, $matching): bool {
        return $job->webhookUrl === $matching->url
            && $job->payload === [
                'event' => WebhookEventEnum::component_created->value,
                'body' => $component->toArray(),
            ]
            && $job->meta === [
                'subscription_id' => $matching->id,
                'event' => WebhookEventEnum::component_created->value,
            ];
    });

    Queue::assertPushed(CallWebhookJob::class, 1);
});

it('dispatches subscriptions configured for all events', function () {
    WebhookSubscription::factory()->allEvents()->create();

    Component::factory()->create();

    Queue::assertPushed(CallWebhookJob::class, 1);
});

it('does not dispatch when no subscriptions match the event', function () {
    WebhookSubscription::factory()
        ->selectedEvents([WebhookEventEnum::component_updated])
        ->create();

    app(DispatchWebhooks::class)->handle(new ComponentCreated(Component::factory()->create()));

    Queue::assertNothingPushed();
});
