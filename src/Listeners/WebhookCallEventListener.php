<?php

namespace Cachet\Listeners;

use Cachet\Models\WebhookAttempt;
use Spatie\WebhookServer\Events\WebhookCallEvent;

class WebhookCallEventListener
{
    public function handle(WebhookCallEvent $event)
    {
        WebhookAttempt::create([
            'subscription_id' => $event->meta['subscription_id'],
            'event' => $event->meta['event'],
            'attempt' => $event->attempt,
            'payload' => json_encode($event->payload),
            'response_code' => $event->response?->getStatusCode(),
            'transfer_time' => $event->transferStats?->getTransferTime(),
        ]);
    }
}
