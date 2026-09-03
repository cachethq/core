<?php

namespace Cachet\Filament\Tables\Columns;

use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ToggleColumn as BaseToggleColumn;

class ToggleColumn extends BaseToggleColumn
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->onIcon(Heroicon::Check)->offIcon(Heroicon::XMark);
    }
}
