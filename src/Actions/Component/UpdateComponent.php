<?php

namespace Cachet\Actions\Component;

use Cachet\Data\ComponentData;
use Cachet\Events\Components\ComponentStatusWasChanged;
use Cachet\Models\Component;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateComponent
{
    use AsAction;

    public function handle(Component $component, ComponentData $data): Component
    {
        $oldStatus = $component->status;

        $component->update(array_filter(
            $data->toArray(),
        ));

        if ($component->wasChanged('status')) {
            ComponentStatusWasChanged::dispatch($component, $oldStatus, $component->status);
        }

        return $component->fresh();
    }
}
