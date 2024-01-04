<?php

namespace Cachet\Actions\Component;

use Cachet\Models\Component;

class DeleteComponent
{
    public function handle(Component $component): void
    {
        $component->subscribers()->detach();

        $component->delete();
    }
}
