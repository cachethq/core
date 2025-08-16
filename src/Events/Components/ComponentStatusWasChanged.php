<?php

namespace Cachet\Events\Components;

use Cachet\Concerns\SendsWebhook;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\WebhookEventEnum;
use Cachet\Models\Component;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ComponentStatusWasChanged
{
    use Dispatchable, InteractsWithSockets, SendsWebhook, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Component $component, public ComponentStatusEnum $oldStatus, public ComponentStatusEnum $newStatus)
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }

    public function getWebhookPayload(): array
    {
        return [
            'component_id' => $this->component->getKey(),
            'old_status' => $this->oldStatus->value,
            'new_status' => $this->newStatus->value,
        ];
    }

    public function getWebhookEventName(): WebhookEventEnum
    {
        return WebhookEventEnum::component_status_changed;
    }
}
