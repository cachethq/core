<?php

namespace Cachet\Actions\Component;

use Cachet\Models\Component;

class DeleteComponent
{
    /**
     * Handle the action.
     */
    public function handle(Component $component): void
    {
        $component->subscribers()->detach();

        $component->delete();
    }
}
