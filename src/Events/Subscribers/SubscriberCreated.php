<?php

namespace Cachet\Events\Subscribers;

use Cachet\Concerns\SendsWebhook;
use Cachet\Enums\WebhookEventEnum;
use Cachet\Models\Subscriber;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubscriberCreated
{
    use Dispatchable, InteractsWithSockets, SendsWebhook, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Subscriber $subscriber)
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }

    public function getWebhookPayload(): array
    {
        return $this->subscriber->toArray();
    }

    public function getWebhookEventName(): WebhookEventEnum
    {
        return WebhookEventEnum::subscriber_created;
    }
}
