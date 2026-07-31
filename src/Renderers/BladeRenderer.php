<?php

namespace Cachet\Renderers;

use Illuminate\Support\Facades\Blade;
use RuntimeException;

class BladeRenderer implements Renderer
{
    /**
     * Render the template using Laravel Blade.
     *
     * Blade compiles echo tags and directives to PHP, so a template body is
     * executable code. Rendering is therefore limited to installations that
     * enable the Blade renderer through the cachet.renderers.blade config value.
     *
     * @throws RuntimeException
     */
    public function render(string $template, array $variables = []): string
    {
        if (! config('cachet.renderers.blade')) {
            throw new RuntimeException('The Blade renderer is disabled. Set CACHET_BLADE_RENDERER=true to render Blade templates.');
        }

        return Blade::render($template, $variables, deleteCachedView: true);
    }
}
