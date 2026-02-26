<?php

namespace Cachet\Actions\ComponentGroup;

use Cachet\Data\Requests\ComponentGroup\CreateComponentGroupRequestData;
use Cachet\Enums\ComponentGroupVisibilityEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Models\ComponentGroup;
use Cachet\Verbs\Events\ComponentGroups\ComponentGroupCreated;
use Cachet\Verbs\Events\Components\ComponentUpdated;

class CreateComponentGroup
{
    /**
     * Handle the action.
     */
    public function handle(CreateComponentGroupRequestData $data): ComponentGroup
    {
        $componentGroup = ComponentGroupCreated::commit(
            name: $data->name,
            order: $data->order ?? 0,
            collapsed: ComponentGroupVisibilityEnum::expanded,
            visible: $data->visible ?? ResourceVisibilityEnum::guest,
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

        return $componentGroup;
    }
}
