<?php

namespace Cachet\Data;

use Cachet\Enums\ComponentStatusEnum;
use Illuminate\Http\Request;

class ComponentData
{
    public function __construct(
        public string $name,
        public ?string $description = null,
        public ?ComponentStatusEnum $status = null,
        public ?string $link = null,
        public ?int $order = null,
        public ?int $group = null,
        public bool $enabled = true
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->input('name'),
            description: $request->input('description'),
            status: $request->enum('status', ComponentStatusEnum::class),
            link: $request->input('link'),
            order: $request->input('order'),
            group: $request->input('component_group_id'),
            enabled: $request->boolean('enabled')
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'link' => $this->link,
            'order' => $this->order,
            'component_group_group' => $this->group,
            'enabled' => $this->enabled,
            'status' => $this->status?->value,
        ];
    }
}
