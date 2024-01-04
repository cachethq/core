<?php

namespace Cachet\Actions\Incident;

use Cachet\Models\Incident;
use Illuminate\Support\Str;

class CreateIncident
{
    public function handle(array $incident): Incident
    {
        return Incident::create(array_merge(
            ['guid' => Str::uuid()],
            $incident
        ));
    }
}
