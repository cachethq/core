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
                'human' => optional($this->status)->getLabel(),
                'value' => optional($this->status)->value,
            ],
            'scheduled' => [
                'human' => optional($this->scheduled_at)->diffForHumans(),
                'string' => optional($this->scheduled_at)->toDateTimeString(),
            ],
            'completed' => [
                'human' => optional($this->completed_at)->diffForHumans(),
                'string' => optional($this->completed_at)->toDateTimeString(),
            ],
            'created' => [
                'human' => optional($this->created_at)->diffForHumans(),
                'string' => optional($this->created_at)->toDateTimeString(),
            ],
            'updated' => [
                'human' => optional($this->updated_at)->diffForHumans(),
                'string' => optional($this->updated_at)->toDateTimeString(),
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
