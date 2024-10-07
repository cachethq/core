<?php

namespace Cachet\Actions\ComponentGroup;

use Cachet\Data\ComponentGroup\CreateComponentGroupData;
use Cachet\Models\Component;
use Cachet\Models\ComponentGroup;

class UpdateComponentGroup
{
    /**
     * Handle the action.
     */
    public function handle(ComponentGroup $componentGroup, CreateComponentGroupData $data): ComponentGroup
    {
        $componentGroup->update($data->except('components')->toArray(),);

        if ($data->components) {
            Component::query()->whereIn('id', $data->components)->update([
                'component_group_id' => $componentGroup->id,
            ]);
        }

        return $componentGroup->fresh();
    }
}
