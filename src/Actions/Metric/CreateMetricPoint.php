<?php

namespace Cachet\Actions\Metric;

use Cachet\Concerns\RecordsMetricObservations;
use Cachet\Data\Metrics\MetricObservation;
use Cachet\Data\Requests\Metric\CreateMetricPointRequestData;
use Cachet\Models\Metric;
use Cachet\Models\MetricPoint;
use Illuminate\Support\Carbon;

class CreateMetricPoint
{
    public function __construct(private readonly RecordsMetricObservations $recorder) {}

    /**
     * Handle the action.
     *
     * The submitted value is now added to the metric's bucket for that
     * moment rather than replacing it, so an observation arriving inside
     * the metric's threshold is no longer thrown away.
     */
    public function handle(Metric $metric, ?CreateMetricPointRequestData $data = null): MetricPoint
    {
        $timestamp = $data->timestamp ?? null;

        return $this->recorder->record($metric, new MetricObservation(
            value: (float) ($data->value ?? $metric->default_value ?? 0),
            recordedAt: $timestamp === null ? null : Carbon::parse($timestamp),
        ));
    }
}
