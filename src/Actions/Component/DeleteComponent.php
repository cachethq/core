<?php

namespace Cachet\Actions\Component;

use Cachet\Models\Component;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteComponent
{
    use AsAction;

    public function handle(Component $component)
    {
        $component->delete();
    }
}
