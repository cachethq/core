<?php

namespace Cachet\Actions\Metric;

use Cachet\Data\Requests\Metric\CreateMetricPointRequestData;
use Cachet\Models\Metric;
use Cachet\Models\MetricPoint;

class CreateMetricPoint
{
    /**
     * Handle the action.
     */
    public function handle(Metric $metric, ?CreateMetricPointRequestData $data = null): MetricPoint
    {
        $lastPoint = $metric->metricPoints()->latest()->first();

        // If the last point was created within the threshold, increment the counter.
        if ($lastPoint && $lastPoint->withinThreshold($metric->threshold, $data->timestamp ?? null)) {
            $lastPoint->increment('counter');

            return $lastPoint;
        }

        return $metric->metricPoints()->create([
            'value' => $data->value ?? $metric->default_value,
            'counter' => 1,
            'created_at' => $data->timestamp ?? now(),
        ]);
    }
}
