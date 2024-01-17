<?php

namespace Cachet\Filament\Widgets;

use Filament\Widgets\Widget;

class Components extends Widget
{
    protected static string $view = 'cachet::filament.widgets.components';

    protected int|string|array $columnSpan = 'full';
}
