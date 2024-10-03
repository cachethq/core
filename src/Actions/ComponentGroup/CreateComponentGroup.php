<?php

namespace Cachet\Actions\ComponentGroup;

use Cachet\Models\Component;
use Cachet\Models\ComponentGroup;

class CreateComponentGroup
{
    /**
     * Handle the action.
     */
    public function handle(array $data, ?array $components = null): ComponentGroup
    {
        return tap(ComponentGroup::create($data), function (ComponentGroup $componentGroup) use ($components) {
            if (! $components) {
                return;
            }

            return Component::query()->whereIn('id', $components)->update([
                'component_group_id' => $componentGroup->id,
            ]);
        });
    }
}
