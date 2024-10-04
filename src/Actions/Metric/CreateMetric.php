<?php

namespace Cachet\Actions\Metric;

use Cachet\Models\Metric;

class CreateMetric
{
    /**
     * Handle the action.
     */
    public function handle(array $data): Metric
    {
        return Metric::create($data);
    }
}
