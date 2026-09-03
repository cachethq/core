<?php

namespace Cachet\Filament\Tables\Columns;

use Cachet\Filament\Support\CachetTimezone;
use Filament\Tables\Columns\TextColumn as BaseTextColumn;

class TextColumn extends BaseTextColumn
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->timezone(CachetTimezone::get(...));
    }
}
