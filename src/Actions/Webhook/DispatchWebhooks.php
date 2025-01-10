<?php

namespace Cachet\Actions\Webhook;

use Cachet\Enums\WebhookEventEnum;
use Cachet\Models\WebhookSubscription;
use Illuminate\Database\Eloquent\Collection;

class DispatchWebhooks
{
    protected mixed $event;

    protected WebhookEventEnum $eventName;

    public function handle(mixed $event): void
    {
        $this->event = $event;
        $this->eventName = $this->event->getWebhookEventName();

        $payload = $this->event->getWebhookPayload();

        foreach ($this->getWebhookSubscriptionsForEvent() as $webhookSubscription) {
            $webhookSubscription->makeCall($this->eventName, $payload)->dispatch();
        }
    }

    /**
     * @return Collection<WebhookSubscription>
     */
    private function getWebhookSubscriptionsForEvent(): Collection
    {
        return WebhookSubscription::whereEvent($this->eventName)->get();
    }
}
