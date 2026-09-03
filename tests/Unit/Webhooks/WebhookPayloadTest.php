<?php

use Cachet\Models\Component;
use Cachet\Models\Incident;
use Cachet\Models\Metric;
use Cachet\Models\MetricPoint;
use Cachet\Models\Subscriber;
use Cachet\Webhooks\WebhookPayload;

it('keeps each webhook resource schema explicit', function (Closure $payload, string $resource, array $attributes) {
    $data = $payload();

    expect($data['resource'])->toBe($resource)
        ->and($data['id'])->toBeInt()
        ->and(array_keys($data['attributes']))->toBe($attributes);
})->with([
    'component' => [
        fn (): array => WebhookPayload::component(Component::factory()->create()),
        'component',
        ['name', 'description', 'link', 'order', 'status', 'group_id', 'enabled', 'checked', 'checked_at', 'created_at', 'updated_at'],
    ],
    'incident' => [
        fn (): array => WebhookPayload::incident(Incident::factory()->create()),
        'incident',
        ['guid', 'name', 'message', 'status', 'visibility', 'stickied', 'notifications', 'user_id', 'external_provider', 'external_id', 'occurred_at', 'published_at', 'created_at', 'updated_at'],
    ],
    'metric' => [
        fn (): array => WebhookPayload::metric(Metric::factory()->create()),
        'metric',
        ['name', 'suffix', 'description', 'default_value', 'calculation_type', 'display_chart', 'show_when_empty', 'decimal_places', 'default_view', 'threshold', 'order', 'visibility', 'created_at', 'updated_at'],
    ],
    'metric point' => [
        fn (): array => WebhookPayload::metricPoint(MetricPoint::factory()->create()),
        'metric_point',
        ['metric_id', 'value', 'counter', 'calculated_value', 'created_at', 'updated_at'],
    ],
    'subscriber' => [
        fn (): array => WebhookPayload::subscriber(Subscriber::factory()->create()),
        'subscriber',
        ['email', 'global', 'verified_at', 'created_at', 'updated_at'],
    ],
]);
