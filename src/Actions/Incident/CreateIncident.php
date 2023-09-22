<?php

declare(strict_types=1);

namespace Cachet\Actions\Incident;

use Cachet\Models\Incident;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateIncident
{
    use AsAction;

    public function handle(array $incident): Incident
    {
        return Incident::create(array_merge(
            ['guid' => Str::uuid()],
            $incident
        ));
    }
}
