<?php

namespace Cachet\Database\Factories;

use Cachet\Enums\IncidentStatusEnum;
use Cachet\Models\Incident;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Incident>
 */
class IncidentFactory extends Factory
{
    protected $model = Incident::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'guid' => fake()->uuid(),
            'name' => fake()->sentence,
            'status' => IncidentStatusEnum::identified->value,
            'message' => fake()->paragraph,
        ];
    }
}
