<?php

namespace Cachet\Events\Subscribers;

use Cachet\Contracts\WebhookEvent;
use Cachet\Enums\WebhookEventEnum;
use Cachet\Models\Subscriber;
use Cachet\Webhooks\WebhookPayload;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubscriberUnsubscribed implements WebhookEvent
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Subscriber $subscriber)
    {
        //
    }

    public function getWebhookPayload(): array
    {
        return WebhookPayload::subscriber($this->subscriber);
    }

    public function getWebhookEventName(): WebhookEventEnum
    {
        return WebhookEventEnum::subscriber_unsubscribed;
    }
}
