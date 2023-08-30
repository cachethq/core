<?php

namespace Cachet\Actions\Metric;

use Cachet\Models\Metric;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateMetric
{
    use AsAction;

    public function handle(array $data): Metric
    {
        return Metric::create($data);
    }
}
