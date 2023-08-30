<?php

namespace Cachet\Actions\Component;

use Cachet\Models\Component;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateComponent
{
    use AsAction;

    public function handle(array $component): Component
    {
        return Component::create($component);
    }
}
