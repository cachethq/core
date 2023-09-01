<?php

use Cachet\Actions\Metric\CreateMetric;
use Cachet\Enums\MetricTypeEnum;

it('can create a metric', function () {
    $data = [
        'name' => 'Foo',
        'suffix' => 'Bar',
        'description' => 'Baz',
        'default_value' => 1.5,
        'calc_type' => MetricTypeEnum::sum,
        'display_chart' => true,
        'places' => 1,
    ];

    $metric = CreateMetric::run($data);

    expect($metric)
        ->name->toBe('Foo')
        ->suffix->toBe('Bar')
        ->description->toBe('Baz')
        ->default_value->toBe(1.5)
        ->calc_type->toBe(MetricTypeEnum::sum)
        ->display_chart->toBe(true)
        ->places->toBe(1);
});
