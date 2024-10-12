<?php

namespace Cachet\Database\Factories;

use Cachet\Enums\IncidentStatusEnum;
use Cachet\Models\Incident;
use Cachet\Models\Update;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Update>
 */
class UpdateFactory extends Factory
{
    protected $model = Update::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'updateable_id' => Incident::factory(),
            'updateable_type' => function (array $attributes) {
                return Incident::find($attributes['updateable_id'])->type;
            },
            'status' => IncidentStatusEnum::identified->value,
            'message' => fake()->paragraph,
            'user_id' => 1, // @todo decide how to handle storing of users... nullable?
        ];
    }
}
