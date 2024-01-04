<?php

namespace Cachet\Actions\ComponentGroup;

use Cachet\Models\Component;
use Cachet\Models\ComponentGroup;

class CreateComponentGroup
{
    public function handle(array $data, ?array $components = []): ComponentGroup
    {
        return tap(ComponentGroup::create($data), function (ComponentGroup $componentGroup) use ($components) {
            if ($components) {
                Component::query()->whereIn('id', $components)->update([
                    'component_group_id' => $componentGroup->id,
                ]);
            }
        });
    }
}
