<?php

namespace Cachet\Actions\Component;

use Cachet\Data\Component\CreateComponentData;
use Cachet\Models\Component;

class CreateComponent
{
    /**
     * Handle the action.
     */
    public function handle(CreateComponentData $component): Component
    {
        return Component::create($component->toArray());
    }
}
