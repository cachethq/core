<?php

namespace Cachet\Actions\Metric;

use Cachet\Data\Requests\Metric\CreateMetricRequestData;
use Cachet\Models\Metric;

class CreateMetric
{
    /**
     * Handle the action.
     */
    public function handle(CreateMetricRequestData $data): Metric
    {
        return Metric::create($data->toArray());
    }
}
