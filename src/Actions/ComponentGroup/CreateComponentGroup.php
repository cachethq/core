<?php

namespace Cachet\Actions\ComponentGroup;

use Cachet\Models\ComponentGroup;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateComponentGroup
{
    use AsAction;

    public function handle(array $data): ComponentGroup
    {
        return ComponentGroup::create($data);
    }
}
