<?php

namespace Cachet\Database\Factories;

use Cachet\Enums\ComponentGroupVisibilityEnum;
use Cachet\Models\ComponentGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ComponentGroup>
 */
class ComponentGroupFactory extends Factory
{
    protected $model = ComponentGroup::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word,
            'order' => 0,
            'visible' => ComponentGroupVisibilityEnum::expanded->value,
        ];
    }
}
