<?php

namespace Cachet\Database\Factories;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Models\Component;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Cachet\Models\Component>
 */
class ComponentFactory extends Factory
{
    protected $model = Component::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word,
            'description' => fake()->paragraph,
            'status' => ComponentStatusEnum::performance_issues->value,
            'order' => 0,
            'component_group_id' => 0,
            'enabled' => true,
        ];
    }

    /**
     * Provide the component with additional meta.
     */
    public function withMeta(): self
    {
        return $this->state([
            'meta' => [
                'foo' => 'bar',
            ],
        ]);
    }
}
