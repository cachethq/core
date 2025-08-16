<?php

namespace Cachet\Database\Factories;

use Cachet\Enums\IncidentTemplateEngineEnum;
use Cachet\Models\IncidentTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<IncidentTemplate>
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
            'name' => $name = fake()->words(2, true),
            'slug' => Str::slug($name),
            'engine' => IncidentTemplateEngineEnum::twig,
            'template' => 'This is an incident template.',
        ];
    }

    public function blade(): self
    {
        return $this->state([
            'engine' => IncidentTemplateEngineEnum::blade,
            'template' => <<<'EOT'
Hey,

A new incident has been reported:

Name: {{ $incident['name'] }}
{{ $incident['message'] }}
EOT,
        ]);
    }

    public function twig(): self
    {
        return $this->state([
            'engine' => IncidentTemplateEngineEnum::twig,
            'template' => <<<'EOT'
Hey,

A new incident has been reported:

Name: {{ incident.name }}
{{ incident.message }}
EOT,
        ]);
    }
}
