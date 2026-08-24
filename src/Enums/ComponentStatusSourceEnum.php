<?php

namespace Cachet\Enums;

use Filament\Support\Contracts\HasLabel;

enum ComponentStatusSourceEnum: string implements HasLabel
{
    case Manual = 'manual';
    case Monitor = 'monitor';
    case Import = 'import';
    case System = 'system';

    public function getLabel(): string
    {
        return __("cachet::component.status_source.{$this->value}");
    }
}
