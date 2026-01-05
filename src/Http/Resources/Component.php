<?php

namespace Cachet\Http\Resources;

use Cachet\Enums\ComponentStatusEnum;
use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

/** @mixin \Cachet\Models\Component */
class Component extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'link' => $this->link,
            'order' => $this->order,
            'status' => [
                'human' => $this->status?->getLabel(),
                'value' => $this->status?->value,
            ],
            'enabled' => $this->enabled,
            'meta' => $this->meta,
            'created' => [
                'human' => $this->created_at?->diffForHumans(),
                'string' => $this->created_at?->toDateTimeString(),
            ],
            'updated' => [
                'human' => $this->updated_at?->diffForHumans(),
                'string' => $this->updated_at?->toDateTimeString(),
            ],
            'pivot' => $this->when($this->pivot?->component_status, function () {
                return [
                    'component_status' => [
                        'human' => ComponentStatusEnum::from($this->pivot->component_status)?->getLabel(),
                        'value' => $this->pivot->component_status,
                    ],
                ];
            }),
        ];
    }

    public function toRelationships(Request $request): array
    {
        return [
            'group' => fn () => ComponentGroup::make($this->group),
            'incidents' => fn () => Incident::collection($this->incidents),
        ];
    }
}
