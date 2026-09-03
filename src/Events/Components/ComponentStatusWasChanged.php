<?php

namespace Cachet\Events\Components;

use Cachet\Contracts\WebhookEvent;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\ComponentStatusSourceEnum;
use Cachet\Enums\WebhookEventEnum;
use Cachet\Models\Component;
use Cachet\Webhooks\WebhookPayload;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ComponentStatusWasChanged implements WebhookEvent
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Component $component,
        public ?ComponentStatusEnum $oldStatus,
        public ComponentStatusEnum $newStatus,
        public ComponentStatusSourceEnum $source = ComponentStatusSourceEnum::Manual,
    ) {
        //
    }

    public function getWebhookPayload(): array
    {
        return [
            ...WebhookPayload::component($this->component),
            'changes' => [
                'old_status' => $this->oldStatus?->value,
                'new_status' => $this->newStatus->value,
                'source' => $this->source->value,
            ],
        ];
    }

    public function getWebhookEventName(): WebhookEventEnum
    {
        return WebhookEventEnum::component_status_changed;
    }
}
