<?php

use Cachet\Actions\Metric\DeleteMetricPoint;
use Cachet\Events\Metrics\MetricPointDeleted;
use Cachet\Models\MetricPoint;
use Illuminate\Support\Facades\Event;

it('can delete a metric point', function () {
    Event::fake();
    $metricPoint = MetricPoint::factory()->create();

    app(DeleteMetricPoint::class)->handle($metricPoint);

    $this->assertDatabaseMissing('metric_points', [
        'id' => $metricPoint->id,
    ]);
    Event::assertDispatched(MetricPointDeleted::class);
});
