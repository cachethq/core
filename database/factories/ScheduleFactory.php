<?php

namespace Cachet\Database\Factories;

use Cachet\Models\Schedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Schedule>
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
            'scheduled_at' => now()->addDays(7),
            'completed_at' => now()->addDays(14),
        ];
    }

    public function completed(): self
    {
        return $this->state([
            'scheduled_at' => now()->subMinutes(45),
            'completed_at' => now()->subMinutes(30),
        ]);
    }

    public function inProgress(): self
    {
        return $this->state([
            'scheduled_at' => now()->subMinutes(30),
            'completed_at' => null,
        ]);
    }

    public function inTheFuture(): self
    {
        return $this->state([
            'scheduled_at' => now()->addDays(30),
            'completed_at' => null,
        ]);
    }

    public function inThePast(): self
    {
        return $this->state([
            'scheduled_at' => now()->subDays(30)->subHours(2),
            'completed_at' => now()->subDays(30),
        ]);
    }
}
