<?php

namespace Cachet\Actions\Component;

use Cachet\Models\Component;

class DeleteComponent
{
    /**
     * Handle the action.
     *
     * The delete is soft, so the component keeps its subscriptions for a
     * restore; the model purges them once it is hard deleted.
     */
    public function handle(Component $component): void
    {
        $component->delete();
    }
}
