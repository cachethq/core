<?php

namespace Cachet\Actions\Component;

use Cachet\Data\Component\UpdateComponentData;
use Cachet\Events\Components\ComponentStatusWasChanged;
use Cachet\Models\Component;

class UpdateComponent
{
    /**
     * Handle the action.
     */
    public function handle(Component $component, UpdateComponentData $data): Component
    {
        $oldStatus = $component->status;

        $component->update($data->toArray());

        if ($component->wasChanged('status')) {
            ComponentStatusWasChanged::dispatch(
                $component,
                $oldStatus,
                $component->status
            );
        }

        return $component->fresh();
    }
}
