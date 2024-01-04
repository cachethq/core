<?php

namespace Cachet\Actions\ComponentGroup;

use Cachet\Models\Component;
use Cachet\Models\ComponentGroup;

class UpdateComponentGroup
{
    public function handle(ComponentGroup $componentGroup, array $data = [], ?array $components = []): ComponentGroup
    {
        $componentGroup->update($data);

        if ($components) {
            Component::query()->whereIn('id', $components)->update([
                'component_group_id' => $componentGroup->id,
            ]);
        }

        return $componentGroup->fresh();
    }
}
