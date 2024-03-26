<?php

namespace Cachet\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ResourceVisibilityEnum: int implements HasColor, HasIcon, HasLabel
{
    case authenticated = 0;
    case guest = 1;
    case hidden = 2;

    public static function visibleToGuests(): array
    {
        return [self::guest];
    }

    public static function visibleToUsers(): array
    {
        return [self::authenticated, self::guest];
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::authenticated => 'heroicon-o-lock-closed',
            self::guest => 'heroicon-o-eye',
            self::hidden => 'heroicon-o-eye-slash',
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::authenticated => __('Users'),
            self::guest => __('Guests'),
            self::hidden => __('Always Hidden'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::authenticated => 'danger',
            self::guest => 'primary',
            self::hidden => 'warning',
        };
    }
}
