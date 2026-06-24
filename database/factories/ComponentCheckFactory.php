<?php

namespace Cachet\Database\Factories;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Models\Component;
use Cachet\Models\ComponentCheck;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ComponentCheck>
 */
class ComponentCheckFactory extends Factory
{
    protected $model = ComponentCheck::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'component_id' => Component::factory(),
            'status' => ComponentStatusEnum::operational,
            'successful' => true,
            'response_code' => 200,
            'response_time' => fake()->numberBetween(20, 800),
            'checked_at' => now(),
        ];
    }
}
