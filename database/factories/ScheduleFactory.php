<?php

namespace Cachet\Database\Factories;

use Cachet\Enums\ScheduleStatusEnum;
use Cachet\Models\Schedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Cachet\Models\Schedule>
 */
class ScheduleFactory extends Factory
{
    protected $model = Schedule::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Incident Schedule',
            'status' => ScheduleStatusEnum::upcoming,
            'scheduled_at' => now()->addDays(7),
        ];
    }
}
