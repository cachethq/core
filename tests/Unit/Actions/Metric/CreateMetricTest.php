<?php

use Cachet\Actions\Metric\CreateMetric;
use Cachet\Data\Requests\Metric\CreateMetricRequestData;
use Cachet\Enums\MetricTypeEnum;
use Cachet\Events\Metrics\MetricCreated;
use Illuminate\Support\Facades\Event;

it('can create a metric', function () {
    Event::fake();
    $data = CreateMetricRequestData::from([
        'name' => 'Foo',
        'suffix' => 'Bar',
        'description' => 'Baz',
        'default_value' => 1.5,
        'calc_type' => MetricTypeEnum::sum,
        'display_chart' => true,
        'places' => 1,
    ]);

    $metric = app(CreateMetric::class)->handle($data);

    expect($metric)
        ->name->toBe('Foo')
        ->suffix->toBe('Bar')
        ->description->toBe('Baz')
        ->default_value->toBe(1.5)
        ->calc_type->toBe(MetricTypeEnum::sum)
        ->display_chart->toBe(true)
        ->places->toBe(1);

    Event::assertDispatched(MetricCreated::class);
});
