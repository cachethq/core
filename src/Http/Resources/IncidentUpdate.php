<?php

declare(strict_types=1);

namespace Cachet\Http\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

/**
 * @mixin \Cachet\Models\IncidentUpdate
 */
class IncidentUpdate extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return [
            'id' => $this->id,
            'message' => $this->message,
            'status' => [
                'human' => $this->status->getName(),
                'value' => $this->status->value,
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
            'incident' => fn () => Incident::make($this->incident),
        ];
    }
}
