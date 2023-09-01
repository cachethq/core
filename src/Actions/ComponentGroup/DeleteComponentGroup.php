<?php

namespace Cachet\Actions\ComponentGroup;

use Cachet\Models\ComponentGroup;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteComponentGroup
{
    use AsAction;

    public function handle(ComponentGroup $componentGroup)
    {
        $componentGroup->components()->update(['component_group_id' => null]);

        $componentGroup->delete();
    }
}
