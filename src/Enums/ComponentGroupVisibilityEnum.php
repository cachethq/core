<?php

namespace Cachet\Enums;

use Filament\Support\Contracts\HasLabel;

enum ComponentGroupVisibilityEnum: int implements HasLabel
{
    case expanded = 0;
    case collapsed = 1;
    case collapsed_unless_incident = 2;

    public function getLabel(): string
    {
        return match ($this->value) {
            self::expanded->value => __('Always expanded'),
            self::collapsed->value => __('Always collapsed'),
            self::collapsed_unless_incident->value => __('Collapsed unless there is an incident in the group'),
        };
    }
}
