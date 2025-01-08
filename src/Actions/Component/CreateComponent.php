<?php

namespace Cachet\Actions\Component;

use Cachet\Data\Requests\Component\CreateComponentRequestData;
use Cachet\Models\Component;

class CreateComponent
{
    /**
     * Handle the action.
     */
    public function handle(CreateComponentRequestData $component): Component
    {
        return Component::create($component->toArray());
    }
}
