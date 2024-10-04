<?php

namespace Cachet\Actions\Metric;

use Cachet\Models\Metric;

class DeleteMetric
{
    /**
     * Handle the action.
     */
    public function handle(Metric $metric): void
    {
        $metric->metricPoints()->delete();
        $metric->delete();
    }
}
