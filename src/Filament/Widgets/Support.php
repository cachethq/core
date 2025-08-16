<?php

namespace Cachet\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Blade;

class Support extends Widget
{
    protected int|string|array $columnSpan = 'full';

    protected string $view = 'cachet::filament.widgets.support';

    public function getConsiderSupportingBlock()
    {
        return preg_replace(
            '/\*(.*?)\*/',
            '<x-filament::link href="https://github.com/cachethq/cachet/?sponsor=1" target="_blank" rel="nofollow noopener">$1</x-filament::link>',
            __('cachet::cachet.support.consider_supporting')
        );
    }

    public function getKeepUpToDateBlock()
    {
        return preg_replace(
            '/\*(.*?)\*/',
            '<x-filament::link href="https://blog.cachethq.io" target="_blank" rel="nofollow noopener">$1</x-filament::link>',
            __('cachet::cachet.support.keep_up_to_date')
        );
    }

    protected function getViewData(): array
    {
        return [
            'considerSupporting' => Blade::render($this->getConsiderSupportingBlock()),
            'keepUpToDate' => Blade::render($this->getKeepUpToDateBlock()),
        ];
    }
}
