<?php

namespace Cachet\Events\Incidents;

use Cachet\Contracts\WebhookEvent;
use Cachet\Enums\WebhookEventEnum;
use Cachet\Models\Incident;
use Cachet\Webhooks\WebhookPayload;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class IncidentDeleted implements WebhookEvent
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Incident $incident)
    {
        //
    }

    public function getWebhookPayload(): array
    {
        return WebhookPayload::incident($this->incident);
    }

    public function getWebhookEventName(): WebhookEventEnum
    {
        return WebhookEventEnum::incident_deleted;
    }
}
