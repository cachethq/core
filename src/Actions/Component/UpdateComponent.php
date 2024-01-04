<?php

namespace Cachet\Actions\Component;

use Cachet\Events\Components\ComponentStatusWasChanged;
use Cachet\Models\Component;

class UpdateComponent
{
    public function handle(Component $component, array $data): Component
    {
        $oldStatus = $component->status;

        $component->update($data);

        if ($component->wasChanged('status')) {
            ComponentStatusWasChanged::dispatch($component, $oldStatus, $component->status);
        }

        return $component->fresh();
    }
}
