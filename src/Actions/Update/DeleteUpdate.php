<?php

namespace Cachet\Actions\Update;

use Cachet\Models\Update;

class DeleteUpdate
{
    /**
     * Handle the action.
     */
    public function handle(Update $update): void
    {
        $update->delete();
    }
}
