<?php

namespace Cachet\Http\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

/**
 * @mixin \Cachet\Models\Incident
 */
class Incident extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return [
            'id' => $this->id,
            'guid' => $this->guid,
            'name' => $this->name,
            'message' => $this->message,
            'component_id' => $this->component_id,
            'visible' => $this->visible,
            'stickied' => $this->stickied,
            'notifications' => $this->notifications,
            'status' => [
                'human' => $this->latestStatus?->getLabel(),
                'value' => $this->latestStatus?->value,
            ],
            'occurred' => [
                'human' => $this->occurred_at?->diffForHumans(),
                'string' => $this->occurred_at?->toDateTimeString(),
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
            'user' => fn () => User::make($this->user),
        ];
    }
}
