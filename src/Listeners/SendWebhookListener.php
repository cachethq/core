<?php

namespace Cachet\Listeners;

use Cachet\Actions\Webhook\DispatchWebhooks;
use Cachet\Concerns\SendsWebhook;

class SendWebhookListener
{
    public function __construct(private DispatchWebhooks $dispatcher) {}

    public function handle(string $eventName, array $data)
    {
        // Does this class use the SendsWebhook trait?
        if (in_array(SendsWebhook::class, class_uses($eventName))) {
            // The instance is in the first element of the data array
            [$eventInstance] = $data;

            $this->dispatcher->handle($eventInstance);
        }
    }
}
