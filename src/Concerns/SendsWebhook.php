<?php

namespace Cachet\Concerns;

use Cachet\Enums\WebhookEventEnum;
use Exception;

trait SendsWebhook
{
    public function getWebhookPayload(): array
    {
        return [];
    }

    public function getWebhookEventName(): WebhookEventEnum
    {
        throw new Exception('You must implement the getWebhookEventName method on '.static::class);
    }
}
