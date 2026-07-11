<?php

namespace Cachet\Http\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

/** @mixin \Cachet\Models\Subscriber */
class Subscriber extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'global' => (bool) $this->global,
            'verified' => $this->hasVerifiedEmail(),
            'verified_at' => [
                'human' => $this->email_verified_at?->diffForHumans(),
                'string' => $this->email_verified_at?->toDateTimeString(),
            ],
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
