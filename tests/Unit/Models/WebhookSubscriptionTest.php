<?php

use Cachet\Enums\WebhookEventEnum;
use Cachet\Models\WebhookSubscription;
use Illuminate\Support\Facades\Bus;
use Spatie\WebhookServer\CallWebhookJob;

it('can have attempts', function () {
    $subscription = WebhookSubscription::factory()->hasAttempts(2)->create();

    expect($subscription->attempts)->toHaveCount(2);
});

it('dispatches webhook calls on the configured queue connection and queue', function () {
    config()->set('cachet.webhooks.queue_connection', 'redis');
    config()->set('cachet.webhooks.queue_name', 'hooks');

    Bus::fake();

    $subscription = WebhookSubscription::factory()->create();

    $subscription->makeCall(WebhookEventEnum::component_created, ['name' => 'API'])->dispatch();

    Bus::assertDispatched(CallWebhookJob::class, function (CallWebhookJob $job) {
        return $job->connection === 'redis' && $job->queue === 'hooks';
    });
});

it('dispatches webhook calls on the default queue connection and queue when not configured', function () {
    Bus::fake();

    $subscription = WebhookSubscription::factory()->create();

    $subscription->makeCall(WebhookEventEnum::component_created, ['name' => 'API'])->dispatch();

    Bus::assertDispatched(CallWebhookJob::class, function (CallWebhookJob $job) {
        return $job->connection === null && $job->queue === null;
    });
});

it('can scope based on given event', function () {
    WebhookSubscription::factory()->sequence(
        ['send_all_events' => true],
        ['send_all_events' => false, 'selected_events' => [WebhookEventEnum::component_created]],
        ['send_all_events' => false, 'selected_events' => [WebhookEventEnum::component_created, WebhookEventEnum::component_updated]],
    )->count(3)->create();

    expect(WebhookSubscription::query()->count())->toBe(3)
        ->and(WebhookSubscription::query()->whereEvent(WebhookEventEnum::component_created)->count())->toBe(3)
        ->and(WebhookSubscription::query()->whereEvent(WebhookEventEnum::component_updated)->count())->toBe(2)
        ->and(WebhookSubscription::query()->whereEvent(WebhookEventEnum::component_status_changed)->count())->toBe(1);
});
