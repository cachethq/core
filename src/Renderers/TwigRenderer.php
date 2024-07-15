<?php

namespace Cachet\Renderers;

use Twig\Environment;
use Twig\Loader\ArrayLoader;

class TwigRenderer implements Renderer
{
    public function __construct() {}

    public function render(string $template, array $variables = []): string
    {
        $env = new Environment(new ArrayLoader([]));

        $template = $env->createTemplate($template);

        return $template->render($variables);
    }
}
