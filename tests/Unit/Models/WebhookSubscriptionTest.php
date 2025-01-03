<?php

use Cachet\Enums\WebhookEventEnum;
use Cachet\Models\WebhookSubscription;

it('can have attempts', function () {
    $subscription = WebhookSubscription::factory()->hasAttempts(2)->create();

    expect($subscription->attempts)->toHaveCount(2);
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
