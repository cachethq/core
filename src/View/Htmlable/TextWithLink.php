<?php

namespace Cachet\View\Htmlable;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Blade;

class TextWithLink implements Htmlable
{
    public function __construct(
        private string $text,
        private string $url,
    ) {}

    public static function make(string $text, string $url): self
    {
        return new self($text, $url);
    }

    private function replaceAsterisksWithComponent(): string
    {
        return preg_replace(
            "/\*(.*)\*/",
            "<x-filament::link href=\"$this->url\" target=\"_blank\" rel=\"nofollow noopener\">$1</x-filament::link>",
            $this->text,
        );
    }

    public function toHtml(): string
    {
        return Blade::render($this->replaceAsterisksWithComponent());
    }
}
