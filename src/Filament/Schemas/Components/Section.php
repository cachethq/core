<?php

namespace Cachet\Filament\Schemas\Components;

use Filament\Schemas\Components\Section as BaseSection;

class Section extends BaseSection
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->columnSpanFull();
    }
}
