<?php

use Cachet\Actions\Webhook\DispatchWebhooks;
use Cachet\Enums\WebhookEventEnum;
use Cachet\Events\Components\ComponentCreated;
use Cachet\Models\Component;
use Cachet\Models\WebhookSubscription;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
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

    $job = Queue::pushed(CallWebhookJob::class)->sole();

    expect($job->webhookUrl)->toBe($matching->url)
        ->and(Str::isUuid($job->payload['id']))->toBeTrue()
        ->and($job->payload['event'])->toBe(WebhookEventEnum::component_created->value)
        ->and(Carbon::parse($job->payload['created_at'])->diffInSeconds(now()))->toBeLessThan(1)
        ->and($job->payload['data'])->toBe([
            'resource' => 'component',
            'id' => $component->id,
            'attributes' => [
                'name' => $component->name,
                'description' => $component->description,
                'link' => $component->link,
                'order' => $component->order,
                'status' => $component->status->value,
                'group_id' => $component->component_group_id,
                'enabled' => $component->enabled,
                'checked' => $component->checked,
                'checked_at' => $component->checked_at?->toISOString(),
                'created_at' => $component->created_at?->toISOString(),
                'updated_at' => $component->updated_at?->toISOString(),
            ],
        ])
        ->and($job->meta)->toBe([
            'subscription_id' => $matching->id,
            'event' => WebhookEventEnum::component_created->value,
        ]);

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
