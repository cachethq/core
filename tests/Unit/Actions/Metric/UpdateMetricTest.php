<?php

use Cachet\Actions\Metric\UpdateMetric;
use Cachet\Models\Metric;

it('can update a metric', function () {
    $metric = Metric::factory()->create();

    $data = [
        'name' => 'Updated Metric',
    ];

    $metric = UpdateMetric::run($metric, $data);

    expect($metric)
        ->name->toBe($data['name']);
});
