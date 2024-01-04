<?php

use Cachet\Actions\Metric\DeleteMetric;
use Cachet\Events\Metrics\MetricDeleted;
use Cachet\Models\Metric;
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
