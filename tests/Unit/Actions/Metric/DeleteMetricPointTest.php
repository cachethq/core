<?php

use Cachet\Actions\Metric\DeleteMetricPoint;
use Cachet\Models\MetricPoint;

it('can delete a metric point', function () {
    $metricPoint = MetricPoint::factory()->create();

    DeleteMetricPoint::run($metricPoint);

    $this->assertDatabaseMissing('metric_points', [
        'id' => $metricPoint->id,
    ]);
});
