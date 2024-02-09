<?php

namespace Cachet\Http\Resources;

use Cachet\Settings\AppSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use TiMacDonald\JsonApi\JsonApiResource;
use TiMacDonald\JsonApi\Link;

class Status extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return [
            'name' => app(AppSettings::class)->name,
            'label' => $this->current()['label'],
            'status' => $this->current()['status'],
        ];
    }

    public function toId(Request $request): string
    {
        return 'status';
    }

    public function toType(Request $request)
    {
        return 'status';
    }
}
