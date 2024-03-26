<?php

namespace Cachet\Filament\Widgets;

use Filament\Widgets\Widget;

class Support extends Widget
{
    protected int|string|array $columnSpan = 'full';

    protected static string $view = 'cachet::filament.widgets.support';
}
