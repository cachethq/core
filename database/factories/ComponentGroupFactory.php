<?php

namespace Cachet\Database\Factories;

use Cachet\Enums\ResourceOrderColumnEnum;
use Cachet\Enums\ResourceOrderDirectionEnum;
use Cachet\Enums\ResourceVisibilityEnum;
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
            'order_column' => ResourceOrderColumnEnum::Manual,
            'order_direction' => null,
            'visible' => ResourceVisibilityEnum::guest->value,
        ];
    }

    /**
     * Order the group's components by the given column and direction.
     */
    public function orderedBy(ResourceOrderColumnEnum $column, ?ResourceOrderDirectionEnum $direction = ResourceOrderDirectionEnum::Asc): static
    {
        return $this->state(fn (array $attributes) => [
            'order_column' => $column,
            'order_direction' => $column === ResourceOrderColumnEnum::Manual ? null : $direction,
        ]);
    }

    /**
     * Provide the component group with additional meta.
     */
    public function withMeta(): self
    {
        return $this->afterCreating(function (ComponentGroup $componentGroup) {
            $componentGroup->meta()->create(['key' => 'foo', 'value' => 'bar']);
        });
    }
}
