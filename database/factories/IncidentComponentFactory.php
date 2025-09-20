<?php

namespace Cachet\Database\Factories;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Models\Component;
use Cachet\Models\Incident;
use Cachet\Models\IncidentComponent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<IncidentComponent>
 */
class IncidentComponentFactory extends Factory
{
    protected $model = IncidentComponent::class;

    public function definition(): array
    {
        return [
            'incident_id' => Incident::factory(),
            'component_id' => Component::factory(),
            'component_status' => ComponentStatusEnum::performance_issues->value,
        ];
    }
}
