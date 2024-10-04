<?php

namespace Cachet\Actions\Component;

use Cachet\Events\Components\ComponentStatusWasChanged;
use Cachet\Models\Component;

class UpdateComponent
{
    /**
     * Handle the action.
     */
    public function handle(Component $component, array $data): Component
    {
        $oldStatus = $component->status;

        $component->update($data);

        if ($component->wasChanged('status')) {
            ComponentStatusWasChanged::dispatch(
                component: $component,
                oldStatus: $oldStatus,
                newStatus: $component->status
            );
        }

        return $component->fresh();
    }
}
