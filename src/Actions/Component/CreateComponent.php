<?php

namespace Cachet\Actions\Component;

use Cachet\Data\ComponentData;
use Cachet\Models\Component;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateComponent
{
    use AsAction;

    public function handle(ComponentData $component): Component
    {
        return Component::create($component->toArray());
    }
}
