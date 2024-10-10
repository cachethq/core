<?php

namespace Cachet\Renderers;

interface Renderer
{
    /**
     * Render a template.
     */
    public function render(string $template, array $variables = []): string;
}
