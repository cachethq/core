<?php

namespace Cachet\Actions\Metric;

use Cachet\Models\Metric;

class CreateMetric
{
    public function handle(array $data): Metric
    {
        return Metric::create($data);
    }
}
