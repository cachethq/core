<?php

namespace Cachet\Actions\ComponentGroup;

use Cachet\Models\ComponentGroup;

class DeleteComponentGroup
{
    public function handle(ComponentGroup $componentGroup)
    {
        $componentGroup->components()->update(['component_group_id' => null]);

        $componentGroup->delete();
    }
}
