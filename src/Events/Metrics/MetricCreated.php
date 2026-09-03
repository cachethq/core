<?php

namespace Cachet\Events\Metrics;

use Cachet\Contracts\WebhookEvent;
use Cachet\Enums\WebhookEventEnum;
use Cachet\Models\Metric;
use Cachet\Webhooks\WebhookPayload;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MetricCreated implements WebhookEvent
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Metric $metric)
    {
        //
    }

    public function getWebhookPayload(): array
    {
        return WebhookPayload::metric($this->metric);
    }

    public function getWebhookEventName(): WebhookEventEnum
    {
        return WebhookEventEnum::metric_created;
    }
}
