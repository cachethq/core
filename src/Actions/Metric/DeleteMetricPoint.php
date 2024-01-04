<?php

namespace Cachet\Actions\Metric;

use Cachet\Models\MetricPoint;

class DeleteMetricPoint
{
    public function handle(MetricPoint $metricPoint): void
    {
        $metricPoint->delete();
    }
}
