<?php

namespace Cachet\Actions\ComponentGroup;

use Cachet\Models\ComponentGroup;

class DeleteComponentGroup
{
    /**
     * Handle the action.
     */
    public function handle(ComponentGroup $componentGroup): void
    {
        $componentGroup->components()->update(['component_group_id' => null]);

        $componentGroup->delete();
    }
}
