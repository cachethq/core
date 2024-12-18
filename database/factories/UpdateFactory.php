<?php

namespace Cachet\Database\Factories;

use Cachet\Enums\IncidentStatusEnum;
use Cachet\Models\Incident;
use Cachet\Models\Schedule;
use Cachet\Models\Update;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\Relation;

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
            'updateable_type' => Relation::getMorphAlias(Incident::class),
            'status' => IncidentStatusEnum::identified->value,
            'message' => fake()->paragraph,
            'user_id' => 1,
        ];
    }

    /**
     * Create an update for an incident.
     */
    public function forIncident(?Incident $incident = null): self
    {
        return $this->state([
            'updateable_id' => $incident->id ?? Incident::factory(),
            'updateable_type' => Relation::getMorphAlias(Incident::class),
            'status' => IncidentStatusEnum::identified->value,
        ]);
    }

    /**
     * Create an update for a schedule.
     */
    public function forSchedule(?Schedule $schedule = null): self
    {
        return $this->state([
            'updateable_id' => $schedule->id ?? Schedule::factory(),
            'updateable_type' => Relation::getMorphAlias(Schedule::class),
            'status' => null,
            'user_id' => null,
        ]);
    }
}
