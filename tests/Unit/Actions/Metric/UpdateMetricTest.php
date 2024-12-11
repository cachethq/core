<?php

use Cachet\Actions\Metric\UpdateMetric;
use Cachet\Data\Metric\UpdateMetricData;
use Cachet\Events\Metrics\MetricUpdated;
use Cachet\Models\Metric;
use Illuminate\Support\Facades\Event;

it('can update a metric', function () {
    Event::fake();
    $metric = Metric::factory()->create();

    $data = UpdateMetricData::from([
        'name' => 'Updated Metric',
    ]);

    $metric = app(UpdateMetric::class)->handle($metric, $data);

    expect($metric)
        ->name->toBe($data->name);
    Event::assertDispatched(MetricUpdated::class);
});
