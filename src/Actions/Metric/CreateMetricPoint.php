<?php

namespace Cachet\Actions\Metric;

use Cachet\Models\Metric;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateMetricPoint
{
    use AsAction;

    public function handle(Metric $metric, array $data = [])
    {
        // @todo create metric points within threshold of metric.
    }
}
