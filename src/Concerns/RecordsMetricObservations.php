<?php

namespace Cachet\Concerns;

use Cachet\Data\Metrics\MetricObservation;
use Cachet\Models\Metric;
use Cachet\Models\MetricPoint;
use Illuminate\Support\Collection;

/**
 * The seam every metric observation enters Cachet through.
 *
 * Cachet binds its own implementation, which folds observations into
 * fixed-width buckets. Monitoring packages that need to record somewhere
 * else, or to bucket differently, rebind this contract.
 */
interface RecordsMetricObservations
{
    /**
     * Record a single observation against a metric.
     */
    public function record(Metric $metric, MetricObservation $observation): MetricPoint;

    /**
     * Record a batch of observations against a metric.
     *
     * @param  iterable<int, MetricObservation>  $observations
     * @return Collection<int, MetricPoint>
     */
    public function recordMany(Metric $metric, iterable $observations): Collection;
}
