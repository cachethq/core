<?php

namespace Cachet\Database\Factories;

use Cachet\Models\IncidentTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Cachet\Models\IncidentTemplate>
 */
class IncidentTemplateFactory extends Factory
{
    protected $model = IncidentTemplate::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence,
            'slug' => fake()->slug,
            'template' => <<<'EOT'
Hey,

A new incident has been reported:

Name: {{ incident_name }}
{{ incident_description }}
EOT,
        ];
    }
}
