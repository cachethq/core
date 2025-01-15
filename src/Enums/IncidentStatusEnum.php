<?php

namespace Cachet\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum IncidentStatusEnum: int implements HasColor, HasIcon, HasLabel
{
    case unknown = 0;
    case investigating = 1;
    case identified = 2;
    case watching = 3;
    case fixed = 4;

    public static function unresolved(): array
    {
        return [
            self::unknown,
            self::investigating,
            self::identified,
            self::watching,
        ];
    }

    public static function fromString(string $value): self
    {
        return match ($value) {
            'investigating' => self::investigating,
            'identified' => self::identified,
            'watching' => self::watching,
            'fixed' => self::fixed,
            default => self::unknown,
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::investigating => __('cachet::incident.status.investigating'),
            self::identified => __('cachet::incident.status.identified'),
            self::watching => __('cachet::incident.status.watching'),
            self::fixed => __('cachet::incident.status.fixed'),
            default => __('cachet::incident.status.reported'),
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::investigating => 'cachet-incident-investigating',
            self::identified => 'cachet-incident-identified',
            self::watching => 'cachet-incident-watching',
            self::fixed => 'cachet-circle-check',
            default => 'cachet-incident',
        };
    }

    public function getColor(): array
    {
        return match ($this) {
            self::investigating => Color::Blue,
            self::watching => Color::Amber,
            self::fixed => Color::Green,
            self::identified => Color::Purple,
            default => Color::Red,
        };
    }
}
