<?php

namespace Cachet\Actions\Metric;

use Cachet\Data\Metric\UpdateMetricData;
use Cachet\Models\Metric;

class UpdateMetric
{
    /**
     * Handle the action.
     */
    public function handle(Metric $metric, UpdateMetricData $data): Metric
    {
        $metric->update($data->toArray());

        return $metric->fresh();
    }
}
