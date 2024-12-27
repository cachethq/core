<?php

namespace Cachet\Database\Factories;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Models\Component;
use Cachet\Models\Schedule;
use Cachet\Models\ScheduleComponent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ScheduleComponent>
 */
class ScheduleComponentFactory extends Factory
{
    protected $model = ScheduleComponent::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'component_id' => Component::factory(),
            'schedule_id' => Schedule::factory(),
            'component_status' => ComponentStatusEnum::performance_issues->value,
        ];
    }
}
