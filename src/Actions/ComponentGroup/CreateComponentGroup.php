<?php

namespace Cachet\Actions\ComponentGroup;

use Cachet\Models\Component;
use Cachet\Models\ComponentGroup;

class CreateComponentGroup
{
    public function handle(array $data, ?array $components = []): ComponentGroup
    {
        return tap(ComponentGroup::create($data), fn (ComponentGroup $componentGroup) => Component::query()->whereIn('id', $components)->update([
            'component_group_id' => $componentGroup->id,
        ]));
    }
}
