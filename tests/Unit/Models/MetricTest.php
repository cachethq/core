<?php

use Cachet\Models\Metric;

it('has points', function () {
    $metric = Metric::factory()->hasPoints(2)->create();

    expect($metric->points)->toHaveCount(2);
});

it('calculates value when using counter', function ($value, $counter, $expected) {
    $metric = Metric::factory()->hasPoints(1, ['value' => $value, 'counter' => $counter])->create();

    expect($metric->points->first()->calculated_value)->toBe($expected);
})->with([
    // value, counter, expected
    [1, 2, 2.0],
    [2, 5, 10.0],
    [5, 5, 25.0],
]);
