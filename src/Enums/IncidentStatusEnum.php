<?php

namespace Cachet\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum IncidentStatusEnum: int implements HasColor, HasIcon, HasLabel
{
    case investigating = 1;
    case identified = 2;
    case watching = 3;
    case fixed = 4;

    public function getLabel(): string
    {
        return match ($this) {
            self::investigating => __('Investigating'),
            self::identified => __('Identified'),
            self::watching => __('Watching'),
            self::fixed => __('Fixed'),
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::investigating => 'cachet-incident-investigating',
            self::identified => 'cachet-incident-identified',
            self::watching => 'cachet-incident-watching',
            self::fixed => 'cachet-incident-fixed',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::investigating => 'warning',
            self::identified => 'purple',
            self::watching => 'info',
            self::fixed => 'success',
        };
    }
}
