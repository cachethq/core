<?php

namespace Cachet\Actions\Metric;

use Cachet\Data\Requests\Metric\UpdateMetricRequestData;
use Cachet\Models\Metric;

class UpdateMetric
{
    /**
     * Handle the action.
     */
    public function handle(Metric $metric, UpdateMetricRequestData $data): Metric
    {
        $metric->update($data->toArray());

        return $metric->fresh();
    }
}
