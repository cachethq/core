<?php

namespace Cachet\Actions\Update;

use Cachet\Models\Update;

class EditUpdate
{
    /**
     * Handle the action.
     */
    public function handle(Update $update, array $data): Update
    {
        $update->update($data);

        return $update->fresh();
    }
}
