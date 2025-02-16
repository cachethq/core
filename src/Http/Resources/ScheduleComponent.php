<?php

namespace Cachet\Http\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

/** @mixin \Cachet\Models\ScheduleComponent */
class ScheduleComponent extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return parent::toAttributes($request);
    }
}
