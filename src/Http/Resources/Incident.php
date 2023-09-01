<?php

namespace Cachet\Http\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

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
            'status' => $this->status,
            'visible' => $this->visible,
            'stickied' => $this->stickied,
            'notifications' => $this->notifications,
            'occurred' => [
                'human' => optional($this->occurred_at)->diffForHumans(),
                'string' => optional($this->occurred_at)->toDateTimeString(),
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

    public function toRelationships(Request $request)
    {
        return [
            'component' => fn () => Component::make($this->component),
            'updates' => fn () => IncidentUpdate::collection($this->updates),
            'user' => fn () => Component::make($this->user),
        ];
    }
}
