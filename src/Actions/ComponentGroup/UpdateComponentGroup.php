<?php

namespace Cachet\Actions\ComponentGroup;

use Cachet\Data\Requests\ComponentGroup\UpdateComponentGroupRequestData;
use Cachet\Models\ComponentGroup;
use Cachet\Verbs\Events\ComponentGroups\ComponentGroupUpdated;
use Cachet\Verbs\Events\Components\ComponentUpdated;

class UpdateComponentGroup
{
    /**
     * Handle the action.
     */
    public function handle(ComponentGroup $componentGroup, UpdateComponentGroupRequestData $data): ComponentGroup
    {
        $result = ComponentGroupUpdated::commit(
            component_group_id: $componentGroup->id,
            name: $data->name,
            order: $data->order,
            collapsed: $data->collapsed ?? null,
            visible: $data->visible,
        );

        // Assign components to the group via update events
        if ($data->components) {
            foreach ($data->components as $componentId) {
                ComponentUpdated::commit(
                    component_id: $componentId,
                    component_group_id: $componentGroup->id,
                );
            }
        }

        return $result;
    }
}
