<?php

declare(strict_types=1);

namespace Cachet\Models;

use Cachet\Database\Factories\IncidentTemplateFactory;
use Cachet\Enums\IncidentTemplateEngineEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Properties
 *
 * @property-read int $id
 * @property string $name
 * @property string $template
 * @property string $slug
 * @property IncidentTemplateEngineEnum $engine
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Methods
 *
 * @method static IncidentTemplateFactory factory($count = null, $state = [])
 */
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
        return '';
    }

    private function renderWithBlade(array $variables = []): string
    {
        return '';
    }
}
