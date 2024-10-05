<?php

namespace Cachet\Actions\ComponentGroup;

use Cachet\Data\ComponentGroup\ComponentGroupData;
use Cachet\Models\Component;
use Cachet\Models\ComponentGroup;

class CreateComponentGroup
{
    /**
     * Handle the action.
     */
    /**
     * Handle the action.
     */
    public function handle(ComponentGroupData $data): ComponentGroup
    {
        return tap(ComponentGroup::create(
            $data->except('components')->toArray(),
        ), function (ComponentGroup $componentGroup) use ($data) {
            if (! $data->components) {
                return;
            }

            Component::query()->whereIn('id', $data->components)->update([
                'component_group_id' => $componentGroup->id,
            ]);
        });
    }
}
