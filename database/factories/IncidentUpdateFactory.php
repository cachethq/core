<?php

namespace Cachet\Database\Factories;

use Cachet\Enums\IncidentStatusEnum;
use Cachet\Models\Incident;
use Cachet\Models\IncidentUpdate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<IncidentUpdate>
 */
class IncidentUpdateFactory extends Factory
{
    protected $model = IncidentUpdate::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'incident_id' => Incident::factory(),
            'status' => IncidentStatusEnum::identified->value,
            'message' => fake()->paragraph,
            'user_id' => 1, // @todo decide how to handle storing of users... nullable?
        ];
    }
}
