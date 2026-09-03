<?php

namespace Cachet\Events\Components;

use Cachet\Contracts\WebhookEvent;
use Cachet\Enums\WebhookEventEnum;
use Cachet\Models\Component;
use Cachet\Webhooks\WebhookPayload;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ComponentDeleted implements WebhookEvent
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Component $component)
    {
        //
    }

    public function getWebhookPayload(): array
    {
        return WebhookPayload::component($this->component);
    }

    public function getWebhookEventName(): WebhookEventEnum
    {
        return WebhookEventEnum::component_deleted;
    }
}
