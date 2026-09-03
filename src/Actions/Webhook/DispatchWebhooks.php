<?php

namespace Cachet\Actions\Webhook;

use Cachet\Contracts\WebhookEvent;
use Cachet\Enums\WebhookEventEnum;
use Cachet\Models\WebhookSubscription;
use Illuminate\Database\Eloquent\Collection;

class DispatchWebhooks
{
    public function handle(WebhookEvent $event): void
    {
        $eventName = $event->getWebhookEventName();
        $webhookSubscriptions = $this->getWebhookSubscriptionsForEvent($eventName);

        if ($webhookSubscriptions->isEmpty()) {
            return;
        }

        $payload = $event->getWebhookPayload();

        foreach ($webhookSubscriptions as $webhookSubscription) {
            $webhookSubscription->makeCall($eventName, $payload)->dispatch();
        }
    }

    /**
     * @return Collection<WebhookSubscription>
     */
    private function getWebhookSubscriptionsForEvent(WebhookEventEnum $event): Collection
    {
        return WebhookSubscription::whereEvent($event)->get();
    }
}
