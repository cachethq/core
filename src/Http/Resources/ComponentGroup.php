<?php

namespace Cachet\Http\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

/** @mixin \Cachet\Models\ComponentGroup */
class ComponentGroup extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'order' => $this->order,
            'order_column' => $this->order_column,
            'order_direction' => $this->order_direction,
            'collapsed' => $this->collapsed,
            'visible' => $this->visible,
            'meta' => $this->when(
                $this->resource->relationLoaded('meta'),
                fn () => $this->meta->pluck('value', 'key'),
            ),
            'created' => [
                'human' => $this->created_at?->diffForHumans(),
                'string' => $this->created_at?->toDateTimeString(),
            ],
            'updated' => [
                'human' => $this->updated_at?->diffForHumans(),
                'string' => $this->updated_at?->toDateTimeString(),
            ],
        ];
    }

    public function toRelationships(Request $request): array
    {
        return [
            'components' => fn () => Component::collection($this->components),
        ];
    }
}
