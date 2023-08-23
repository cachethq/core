<?php

namespace Cachet\Actions\Metric;

use Cachet\Data\MetricData;
use Cachet\Models\Metric;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateMetric
{
    use AsAction;

    public function handle(MetricData $data): Metric
    {
        return Metric::create($data->toArray());
    }
}
