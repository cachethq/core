<?php

namespace Cachet\Actions\Component;

use Cachet\Data\Requests\Component\UpdateComponentRequestData;
use Cachet\Events\Components\ComponentStatusWasChanged;
use Cachet\Models\Component;

class UpdateComponent
{
    /**
     * Handle the action.
     */
    public function handle(Component $component, UpdateComponentRequestData $data): Component
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
