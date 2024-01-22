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

    public static function unresolved(): array
    {
        return [
            self::investigating,
            self::identified,
            self::watching,
        ];
    }

    public static function fromString(string $value): self
    {
        return match($value) {
            'investigating' => self::investigating,
            'identified' => self::identified,
            'watching' => self::watching,
            'fixed' => self::fixed,
        };
    }

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
            self::identified => 'gray',
            self::watching => 'info',
            self::fixed => 'success',
        };
    }
}
