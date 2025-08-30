<?php

namespace Cachet\Database\Factories;

use Cachet\Enums\MetricTypeEnum;
use Cachet\Enums\MetricViewEnum;
use Cachet\Models\Metric;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Metric>
 */
class MetricFactory extends Factory
{
    protected $model = Metric::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Cups of coffee',
            'suffix' => 'cups',
            'description' => 'How many cups of coffee consumed by the team.',
            'default_value' => 1,
            'calc_type' => MetricTypeEnum::sum->value,
            'default_view' => MetricViewEnum::today->value,
            'threshold' => 5, // 5 minutes.
        ];
    }
}
