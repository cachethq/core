<?php

namespace Cachet\Http\Resources;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

/**
 * @mixin Authenticatable
 */
class User extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return [
            'id' => $this->getAuthIdentifier(),
        ];
    }
}
