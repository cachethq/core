<?php

namespace Cachet\Actions\Metric;

use Cachet\Models\MetricPoint;

class DeleteMetricPoint
{
    /**
     * Handle the action.
     */
    public function handle(MetricPoint $metricPoint): void
    {
        $metricPoint->delete();
    }
}
