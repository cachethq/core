<?php

use Cachet\Renderers\BladeRenderer;
use Cachet\Renderers\Renderer;
use Cachet\Renderers\TwigRenderer;

test('renderers test')
    ->expect([
        BladeRenderer::class,
        TwigRenderer::class,
    ])
    ->toBeClasses()
    ->toImplement(Renderer::class)
    ->toHaveSuffix('Renderer');
