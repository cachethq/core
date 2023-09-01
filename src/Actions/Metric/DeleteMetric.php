<?php

namespace Cachet\Actions\Metric;

use Cachet\Models\Metric;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteMetric
{
    use AsAction;

    public function handle(Metric $metric)
    {
        $metric->metricPoints()->delete();
        $metric->delete();
    }
}
