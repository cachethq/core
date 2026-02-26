<?php

namespace Cachet\Actions\ComponentGroup;

use Cachet\Models\ComponentGroup;
use Cachet\Verbs\Events\ComponentGroups\ComponentGroupDeleted;
use Cachet\Verbs\Events\Components\ComponentUpdated;

class DeleteComponentGroup
{
    /**
     * Handle the action.
     */
    public function handle(ComponentGroup $componentGroup): void
    {
        // First, unassign all components from this group
        foreach ($componentGroup->components as $component) {
            ComponentUpdated::commit(
                component_id: $component->id,
                clear_component_group: true,
            );
        }

        ComponentGroupDeleted::commit(component_group_id: $componentGroup->id);
    }
}
