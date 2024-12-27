<?php

namespace Cachet\Models;

use Cachet\Database\Factories\IncidentTemplateFactory;
use Cachet\Enums\IncidentTemplateEngineEnum;
use Cachet\Renderers\BladeRenderer;
use Cachet\Renderers\TwigRenderer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $template
 * @property ?Carbon $created_at
 * @property Carbon $update_at
 * @property ?Carbon $deleted_at
 * @property IncidentTemplateEngineEnum $engine
 *
 * @method static IncidentTemplateFactory factory($count = null, $state = [])
 */
class IncidentTemplate extends Model
{
    /** @use HasFactory<IncidentTemplateFactory> */
    use HasFactory;

    /** @var array<string, string> */
    protected $casts = [
        'engine' => IncidentTemplateEngineEnum::class,
    ];

    /** @var list<string> */
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

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return IncidentTemplateFactory::new();
    }
}
