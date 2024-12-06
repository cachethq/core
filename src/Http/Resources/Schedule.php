<?php

namespace Cachet\Http\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

/** @mixin \Cachet\Models\Schedule */
class Schedule extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'message' => $this->message,
            'status' => [
                'human' => $this->status->getLabel(),
                'value' => $this->status->value,
            ],
            'scheduled' => [
                'human' => $this->scheduled_at->diffForHumans(),
                'string' => $this->scheduled_at->toDateTimeString(),
            ],
            'completed' => [
                'human' => $this->completed_at?->diffForHumans(),
                'string' => $this->completed_at?->toDateTimeString(),
            ],
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
            'updates' => fn () => Update::collection($this->updates),
        ];
    }
}
