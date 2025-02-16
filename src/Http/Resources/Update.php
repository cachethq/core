<?php

namespace Cachet\Http\Resources;

use Cachet\Models\Incident;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

/**
 * @mixin \Cachet\Models\Update
 */
class Update extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return [
            'id' => $this->id,
            'updateable_id' => $this->updateable_id,
            'updateable_type' => $this->updateable_type,
            'message' => $this->message,
            'status' => $this->when($this->updateable_type === Relation::getMorphAlias(Incident::class), [
                'human' => $this->status?->getLabel(),
                'value' => $this->status?->value,
            ]),
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
            //            'incident' => fn () => Incident::make($this->incident),
        ];
    }
}
