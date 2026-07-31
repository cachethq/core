<?php

namespace Cachet\Renderers;

use Cachet\Enums\IncidentTemplateEngineEnum;
use Illuminate\Support\Facades\Blade;
use RuntimeException;

class BladeRenderer implements Renderer
{
    /**
     * Render the template using Laravel Blade.
     *
     * Blade compiles echo tags and directives to PHP, so a template body is
     * executable code. Rendering is therefore limited to installations that
     * opt in through the cachet.incident_templates.allow_blade config value.
     *
     * @throws RuntimeException
     */
    public function render(string $template, array $variables = []): string
    {
        if (! IncidentTemplateEngineEnum::blade->isAvailable()) {
            throw new RuntimeException('Blade incident templates are disabled. Set CACHET_ALLOW_BLADE_TEMPLATES=true to render them.');
        }

        return Blade::render($template, $variables, deleteCachedView: true);
    }
}
