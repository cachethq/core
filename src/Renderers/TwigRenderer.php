<?php

namespace Cachet\Renderers;

use Twig\Environment;
use Twig\Extension\SandboxExtension;
use Twig\Loader\ArrayLoader;
use Twig\Sandbox\SecurityPolicy;

class TwigRenderer implements Renderer
{
    /**
     * The Twig tags permitted within incident templates.
     *
     * @var list<string>
     */
    private const ALLOWED_TAGS = [
        'apply',
        'autoescape',
        'for',
        'if',
        'set',
        'spaceless',
        'verbatim',
    ];

    /**
     * The Twig filters permitted within incident templates.
     *
     * Filters that evaluate arbitrary callables (map, filter, reduce, sort, ...)
     * are intentionally excluded as they allow remote code execution.
     *
     * @var list<string>
     */
    private const ALLOWED_FILTERS = [
        'abs',
        'capitalize',
        'date',
        'date_modify',
        'default',
        'e',
        'escape',
        'first',
        'format',
        'join',
        'json_encode',
        'keys',
        'last',
        'length',
        'lower',
        'nl2br',
        'number_format',
        'raw',
        'replace',
        'reverse',
        'round',
        'slice',
        'split',
        'striptags',
        'title',
        'trim',
        'upper',
        'url_encode',
    ];

    /**
     * The Twig functions permitted within incident templates.
     *
     * @var list<string>
     */
    private const ALLOWED_FUNCTIONS = [
        'date',
        'max',
        'min',
        'range',
    ];

    public function render(string $template, array $variables = []): string
    {
        $environment = new Environment(new ArrayLoader([]));

        $securityPolicy = new SecurityPolicy(
            self::ALLOWED_TAGS,
            self::ALLOWED_FILTERS,
            [],
            [],
            self::ALLOWED_FUNCTIONS,
        );

        $environment->addExtension(new SandboxExtension($securityPolicy, sandboxed: true));

        return $environment->createTemplate($template)->render($variables);
    }
}
