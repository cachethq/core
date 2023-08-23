<?php

namespace Cachet\Data;

use Cachet\Enums\IncidentStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class IncidentData
{
    public function __construct(
        public string $name,
        public ?string $message = null,
        public ?int $componentId = null,
        public ?IncidentStatusEnum $status = null,
        public bool $enabled = true,
        public bool $visible = true,
        public bool $stickied = false,
        public bool $sendNotifications = false,
    ) {

    }

    public function toArray(): array
    {
        return [
            'guid' => Str::uuid(),
            'name' => $this->name,
            'message' => $this->message,
            'component_id' => $this->componentId,
            'status' => $this->status?->value,
            'enabled' => $this->enabled,
            'visible' => $this->visible,
            'stickied' => $this->stickied,
            'notifications' => $this->sendNotifications,
        ];
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->input('name'),
            message: $request->input('message'),
            componentId: $request->input('component_id'),
            status: $request->enum('status', IncidentStatusEnum::class),
            enabled: $request->boolean('enabled'),
            visible: $request->boolean('visible'),
            stickied: $request->boolean('stickied'),
            sendNotifications: $request->boolean('notifications')
        );
    }
}
