<?php

declare(strict_types=1);

namespace Cachet\Http\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;
use TiMacDonald\JsonApi\JsonApiResourceCollection;

/**
 * @mixin \Cachet\Models\ComponentGroup
 */
class ComponentGroup extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'order' => $this->order,
            'visible' => $this->visible,
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
            'components' => fn () => Component::collection($this->components),
        ];
    }
}
