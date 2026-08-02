<?php

use Cachet\Actions\Metric\DeleteMetric;
use Cachet\Events\Metrics\MetricDeleted;
use Cachet\Models\Metric;
use Cachet\Models\MetricPoint;
use Illuminate\Support\Facades\Event;

it('can delete a metric', function () {
    Event::fake();
    $metric = Metric::factory()->create();

    app(DeleteMetric::class)->handle($metric);

    $this->assertDatabaseMissing('metrics', [
        'id' => $metric->id,
    ]);
    Event::assertDispatched(MetricDeleted::class);
});

it('keeps the metric points when the delete fails part-way', function () {
    $metric = Metric::factory()->create();
    $metricPoint = MetricPoint::factory()->create(['metric_id' => $metric->id]);

    Metric::deleted(fn () => throw new RuntimeException('boom'));

    try {
        app(DeleteMetric::class)->handle($metric);
    } catch (RuntimeException) {
        //
    }

    $this->assertDatabaseHas('metrics', ['id' => $metric->id]);
    $this->assertDatabaseHas('metric_points', ['id' => $metricPoint->id]);
});
