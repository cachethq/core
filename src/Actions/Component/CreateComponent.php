<?php

namespace Cachet\Actions\Component;

use Cachet\Data\Component\ComponentData;
use Cachet\Models\Component;

class CreateComponent
{
    /**
     * Handle the action.
     */
    public function handle(ComponentData $component): Component
    {
        return Component::create($component->toArray());
    }
}
