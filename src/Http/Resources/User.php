<?php

namespace Cachet\Http\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

/**
 * @mixin \Illuminate\Contracts\Auth\Authenticatable
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
