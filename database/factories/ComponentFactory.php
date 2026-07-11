<?php

namespace Cachet\Database\Factories;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Models\Component;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Component>
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
            'component_group_id' => null,
            'enabled' => true,
        ];
    }

    /**
     * Create a component that is enabled.
     */
    public function enabled(): self
    {
        return $this->state([
            'enabled' => true,
        ]);
    }

    /**
     * Create a component that is monitored via a link check.
     */
    public function checked(): self
    {
        return $this->state([
            'checked' => true,
            'link' => fake()->url(),
        ]);
    }

    /**
     * Create a component that is disabled
     */
    public function disabled(): self
    {
        return $this->state([
            'enabled' => false,
        ]);
    }

    /**
     * Provide the component with additional meta.
     */
    public function withMeta(): self
    {
        return $this->afterCreating(function (Component $component) {
            $component->meta()->create(['key' => 'foo', 'value' => 'bar']);
        });
    }
}
