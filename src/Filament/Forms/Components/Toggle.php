<?php

namespace Cachet\Filament\Forms\Components;

use Filament\Forms\Components\Toggle as BaseToggle;
use Filament\Support\Icons\Heroicon;

class Toggle extends BaseToggle
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->onIcon(Heroicon::Check)->offIcon(Heroicon::XMark);
    }
}
