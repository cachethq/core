<?php

namespace Cachet\Actions\Component;

use Cachet\Data\ComponentData;
use Cachet\Models\Component;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateComponent
{
    use AsAction;

    public function handle(Component $component, array $data): Component
    {
        $component->update($data);

        return $component->fresh();
    }
}
