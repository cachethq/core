<?php

namespace Cachet\Actions\Component;

use Cachet\Models\Component;
use Cachet\Verbs\Events\Components\ComponentDeleted;

class DeleteComponent
{
    /**
     * Handle the action.
     */
    public function handle(Component $component): void
    {
        ComponentDeleted::commit(component_id: $component->id);
    }
}
