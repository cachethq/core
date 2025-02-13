<?php

namespace Cachet\Http\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class Tag extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return parent::toAttributes($request);
    }
}
