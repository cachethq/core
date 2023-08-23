<?php

namespace Cachet\Models;

use Cachet\Enums\IncidentTemplateEngineEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidentTemplate extends Model
{
    use HasFactory;

    protected $casts = [
        'engine' => IncidentTemplateEngineEnum::class,
    ];

    protected $fillable = [
        'name',
        'template',
        'slug',
        'engine',
    ];

    /**
     * Render a template.
     */
    public function render(array $variables = []): string
    {
        return match ($this->engine) {
            IncidentTemplateEngineEnum::blade => $this->renderWithBlade($variables),
            IncidentTemplateEngineEnum::twig => $this->renderWithTwig($variables),
        };
    }

    private function renderWithTwig(array $variables = []): string
    {

    }

    private function renderWithBlade(array $variables = []): string
    {

    }
}
