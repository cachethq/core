<?php

namespace Cachet\Actions\Metric;

use Cachet\Data\Metric\CreateMetricData;
use Cachet\Models\Metric;

class CreateMetric
{
    /**
     * Handle the action.
     */
    public function handle(CreateMetricData $data): Metric
    {
        return Metric::create($data->toArray());
    }
}
