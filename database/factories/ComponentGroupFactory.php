<?php

namespace Cachet\Database\Factories;

use Cachet\Enums\ComponentGroupVisibilityEnum;
use Cachet\Enums\ResourceOrderColumnEnum;
use Cachet\Enums\ResourceOrderDirectionEnum;
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
            'visible' => ComponentGroupVisibilityEnum::expanded->value,
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
}
