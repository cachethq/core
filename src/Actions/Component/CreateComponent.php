<?php

namespace Cachet\Actions\Component;

use Cachet\Models\Component;

class CreateComponent
{
    /**
     * Handle the action.
     */
    public function handle(array $component): Component
    {
        return Component::create($component);
    }
}
