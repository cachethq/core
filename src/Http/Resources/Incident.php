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
                'human' => $this->latestStatus->getLabel(),
                'value' => $this->latestStatus->value,
            ],
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
            'components' => fn () => Component::collection($this->components),
            'updates' => fn () => Update::collection($this->updates),
        ];
    }
}
