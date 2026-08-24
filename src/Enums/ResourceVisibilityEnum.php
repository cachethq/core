<?php

namespace Cachet\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

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

    public function getIcon(): Heroicon
    {
        return match ($this) {
            self::authenticated => Heroicon::OutlinedLockClosed,
            self::guest => Heroicon::OutlinedEye,
            self::hidden => Heroicon::OutlinedEyeSlash,
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::authenticated => __('cachet::resource.visibility.authenticated'),
            self::guest => __('cachet::resource.visibility.guest'),
            self::hidden => __('cachet::resource.visibility.hidden'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::authenticated => 'warning',
            self::guest => 'info',
            self::hidden => 'danger',
        };
    }
}
