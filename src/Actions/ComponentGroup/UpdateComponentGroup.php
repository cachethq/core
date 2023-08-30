<?php

namespace Cachet\Actions\ComponentGroup;

use Cachet\Models\ComponentGroup;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateComponentGroup
{
    use AsAction;

    public function handle(ComponentGroup $componentGroup, array $data): ComponentGroup
    {
        $componentGroup->update($data);

        return $componentGroup->fresh();
    }
}
