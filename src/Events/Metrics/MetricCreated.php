<?php

namespace Cachet\Events\Metrics;

use Cachet\Concerns\SendsWebhook;
use Cachet\Enums\WebhookEventEnum;
use Cachet\Models\Metric;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MetricCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels, SendsWebhook;

    /**
     * Create a new event instance.
     */
    public function __construct(public Metric $metric)
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }

    public function getWebhookPayload(): array
    {
        return $this->metric->toArray();
    }

    public function getWebhookEventName(): WebhookEventEnum
    {
        return WebhookEventEnum::metric_created;
    }
}
