<?php

namespace Cachet\Contracts;

use Cachet\Enums\WebhookEventEnum;

interface WebhookEvent
{
    /**
     * @return array{resource: string, id: int, attributes: array<string, mixed>, changes?: array<string, mixed>}
     */
    public function getWebhookPayload(): array;

    public function getWebhookEventName(): WebhookEventEnum;
}
