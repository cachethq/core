<?php

namespace Cachet\Database\Factories;

use Cachet\Models\Metric;
use Cachet\Models\MetricPoint;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MetricPoint>
 */
class MetricPointFactory extends Factory
{
    protected $model = MetricPoint::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'metric_id' => Metric::factory(),
            'value' => 1,
            'counter' => 1,
        ];
    }
}
