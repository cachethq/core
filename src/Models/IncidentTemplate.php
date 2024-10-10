<?php

namespace Cachet\Models;

use Cachet\Enums\IncidentTemplateEngineEnum;
use Cachet\Renderers\BladeRenderer;
use Cachet\Renderers\TwigRenderer;
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

    /**
     * Render a template using Twig.
     */
    private function renderWithTwig(array $variables = []): string
    {
        return app(TwigRenderer::class)->render($this->template, $variables);
    }

    /**
     * Render a template using Laravel Blade.
     */
    private function renderWithBlade(array $variables = []): string
    {
        return app(BladeRenderer::class)->render($this->template, $variables);
    }
}
