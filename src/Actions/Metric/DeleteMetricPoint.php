<?php

namespace Cachet\Actions\Metric;

use Cachet\Models\MetricPoint;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteMetricPoint
{
    use AsAction;

    public function handle(MetricPoint $metricPoint): void
    {
        $metricPoint->delete();
    }
}
