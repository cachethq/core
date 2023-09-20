<?php

namespace Cachet\Actions\Component;

use Cachet\Data\ComponentData;
use Cachet\Models\Component;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateComponent
{
    use AsAction;

    public function handle(ComponentData $data): Component
    {
        return Component::create($data->toArray());
    }
}
