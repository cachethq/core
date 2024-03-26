<?php

namespace Cachet\Renderers;

use Illuminate\Support\Facades\Blade;

class BladeRenderer implements Renderer
{
    /**
     * Render the template using Laravel Blade.
     */
    public function render(string $template, array $variables = []): string
    {
        return Blade::render($template, $variables, deleteCachedView: true);
    }
}
