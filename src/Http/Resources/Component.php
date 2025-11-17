<?php

namespace Cachet\Http\Resources;

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
            'pivot' => $this->when($this->pivot, function () {
                return [
                    'component_status' => $this->pivot->component_status ? [
                        'human' => $this->pivot->component_status->getLabel(),
                        'value' => $this->pivot->component_status->value,
                    ] : null,
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
