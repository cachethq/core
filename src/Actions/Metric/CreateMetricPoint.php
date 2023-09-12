<?php

namespace Cachet\Actions\Metric;

use Cachet\Models\Metric;
use Cachet\Models\MetricPoint;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateMetricPoint
{
    use AsAction;

    public function handle(Metric $metric, array $data = []): MetricPoint
    {
        $lastPoint = $metric->metricPoints()->latest()->first();

        // If the last point was created within the threshold, increment the counter.
        if ($lastPoint && $lastPoint->withinThreshold($metric->threshold)) {
            $lastPoint->increment('counter');

            return $lastPoint;
        }

        return $metric->metricPoints()->create([
            'value' => $data['value'] ?? $metric->default_value,
            'counter' => 1,
        ]);
    }
}
