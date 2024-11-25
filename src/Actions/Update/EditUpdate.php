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
        return tap($update, function (Update $update) use ($data) {
            $update->update($data);
        });
    }
}
