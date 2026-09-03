<?php

namespace Cachet\Events\Metrics;

use Cachet\Contracts\WebhookEvent;
use Cachet\Enums\WebhookEventEnum;
use Cachet\Models\MetricPoint;
use Cachet\Webhooks\WebhookPayload;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MetricPointCreated implements WebhookEvent
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public MetricPoint $metric)
    {
        //
    }

    public function getWebhookPayload(): array
    {
        return WebhookPayload::metricPoint($this->metric);
    }

    public function getWebhookEventName(): WebhookEventEnum
    {
        return WebhookEventEnum::metric_point_created;
    }
}
