<?php

use Cachet\Models\WebhookAttempt;
use Cachet\Models\WebhookSubscription;
use Illuminate\Support\Carbon;

it('is successful when the response code is 2xx', function (int $code) {
    $attempt = WebhookAttempt::factory()->make(['response_code' => $code]);

    expect($attempt->isSuccess())->toBeTrue();
})->with([200, 201, 204, 299]);

it('is not successful when the response code is outside 2xx', function (?int $code) {
    $attempt = WebhookAttempt::factory()->make(['response_code' => $code]);

    expect($attempt->isSuccess())->toBeFalse();
})->with([100, 199, 300, 400, 500, null]);

it('scopes to successful attempts', function () {
    $subscription = WebhookSubscription::factory()->create();
    WebhookAttempt::factory()->count(2)->create(['subscription_id' => $subscription->id, 'response_code' => 200]);
    WebhookAttempt::factory()->create(['subscription_id' => $subscription->id, 'response_code' => 500]);
    WebhookAttempt::factory()->create(['subscription_id' => $subscription->id, 'response_code' => 404]);

    expect(WebhookAttempt::query()->whereSuccessful()->count())->toBe(2);
});

it('prunes attempts older than the configured retention window', function () {
    config(['cachet.webhooks.logs.prune_logs_after_days' => 7]);
    $subscription = WebhookSubscription::factory()->create();

    $fresh = WebhookAttempt::factory()->create([
        'subscription_id' => $subscription->id,
        'created_at' => Carbon::now()->subDays(1),
    ]);
    $stale = WebhookAttempt::factory()->create([
        'subscription_id' => $subscription->id,
        'created_at' => Carbon::now()->subDays(30),
    ]);

    $prunable = (new WebhookAttempt)->prunable()->pluck('id');

    expect($prunable->all())->toBe([$stale->id])
        ->and($prunable)->not->toContain($fresh->id);
});
