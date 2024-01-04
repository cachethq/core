<?php

namespace Cachet\Actions\Metric;

use Cachet\Models\Metric;

class DeleteMetric
{
    public function handle(Metric $metric)
    {
        $metric->metricPoints()->delete();
        $metric->delete();
    }
}
