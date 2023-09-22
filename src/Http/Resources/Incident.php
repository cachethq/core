<?php

declare(strict_types=1);

namespace Cachet\Http\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;
use TiMacDonald\JsonApi\JsonApiResourceCollection;

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
                'human' => $this->status->getName(),
                'value' => $this->status->value,
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

    /**
     * @return array<string, (callable(): JsonApiResourceCollection)>
     */
    public function toRelationships(Request $request): array
    {
        return [
            'component' => fn () => Component::make($this->component),
            'updates' => fn () => IncidentUpdate::collection($this->incidentUpdates),
            'user' => fn () => Component::make($this->user),
        ];
    }
}
