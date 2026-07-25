<?php

namespace Cachet\Database\Factories;

use Cachet\Models\Metric;
use Cachet\Models\MetricPoint;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<MetricPoint>
 */
class MetricPointFactory extends Factory
{
    protected $model = MetricPoint::class;

    /**
     * How many points this factory has made.
     *
     * A metric holds at most one point per timestamp, so generated points
     * are spread a minute apart rather than all landing on now().
     */
    private static int $made = 0;

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
            'created_at' => fn (): Carbon => Carbon::now()->startOfMinute()->subMinutes(self::$made++ % 1440),
        ];
    }
}
