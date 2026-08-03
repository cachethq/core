<?php

namespace Cachet\Renderers;

use Twig\Environment;
use Twig\Extension\SandboxExtension;
use Twig\Loader\ArrayLoader;
use Twig\Sandbox\SecurityPolicy;

class TwigRenderer implements Renderer
{
    /**
     * The Twig tags an incident template may use.
     *
     * @var list<string>
     */
    private const ALLOWED_TAGS = ['apply', 'for', 'if', 'set', 'verbatim', 'with'];

    /**
     * The Twig filters an incident template may use. Filters that take a callable
     * (map, filter, reduce, sort) are omitted, because a template can pass the
     * name of any PHP function to them.
     *
     * @var list<string>
     */
    private const ALLOWED_FILTERS = [
        'abs', 'capitalize', 'date', 'default', 'e', 'escape', 'first', 'format', 'join', 'keys',
        'last', 'length', 'lower', 'nl2br', 'number_format', 'raw', 'replace', 'reverse', 'round',
        'slice', 'split', 'striptags', 'title', 'trim', 'upper', 'url_encode',
    ];

    public function __construct() {}

    public function render(string $template, array $variables = []): string
    {
        $env = new Environment(new ArrayLoader([]));

        $env->addExtension(new SandboxExtension(
            new SecurityPolicy(self::ALLOWED_TAGS, self::ALLOWED_FILTERS),
            sandboxed: true,
        ));

        $template = $env->createTemplate($template);

        return $template->render($variables);
    }
}
