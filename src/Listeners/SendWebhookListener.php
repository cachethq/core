<?php

namespace Cachet\Listeners;

use Cachet\Actions\Webhook\DispatchWebhooks;
use Cachet\Contracts\WebhookEvent;

class SendWebhookListener
{
    public function __construct(private DispatchWebhooks $dispatcher) {}

    public function handle(string $eventName, array $data): void
    {
        $event = $data[0] ?? null;

        if (! $event instanceof WebhookEvent) {
            return;
        }

        $this->dispatcher->handle($event);
    }
}
