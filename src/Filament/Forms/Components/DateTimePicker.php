<?php

namespace Cachet\Filament\Forms\Components;

use Cachet\Filament\Support\CachetTimezone;
use Filament\Forms\Components\DateTimePicker as BaseDateTimePicker;

class DateTimePicker extends BaseDateTimePicker
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->timezone(CachetTimezone::get(...));
    }
}
