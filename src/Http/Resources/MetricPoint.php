<?php

namespace Cachet\Http\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

/** @mixin \Cachet\Models\MetricPoint */
class MetricPoint extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return [
            'id' => $this->id,
            'metric_id' => $this->metric_id,
            'calculated_value' => $this->calculated_value,
            'value' => $this->value,
            'counter' => $this->counter,
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
            'metric' => fn () => Metric::make($this->metric),
        ];
    }
}
