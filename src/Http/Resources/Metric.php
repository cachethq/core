<?php

namespace Cachet\Http\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class Metric extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'suffix' => $this->suffix,
            'description' => $this->description,
            'default_value' => $this->default_value,
            'calc_type' => $this->calc_type,
            'display_chart' => $this->display_chart,
            'places' => $this->places,
            'default_view' => $this->default_view,
            'threshold' => $this->threshold,
            'order' => $this->order,
            'visible' => $this->visible,
            'status' => $this->status,
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
            'points' => fn () => MetricPoint::collection('points'),
        ];
    }
}
