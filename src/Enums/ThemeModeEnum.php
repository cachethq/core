<?php

namespace Cachet\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ThemeModeEnum: string implements HasIcon, HasLabel
{
    case auto = 'auto';
    case light = 'light';
    case dark = 'dark';

    /**
     * Determine whether this mode forces the status page into a single color scheme.
     */
    public function isForced(): bool
    {
        return $this !== self::auto;
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::auto => 'heroicon-o-computer-desktop',
            self::light => 'heroicon-o-sun',
            self::dark => 'heroicon-o-moon',
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::auto => __('cachet::settings.manage_theme.theme_mode.modes.auto'),
            self::light => __('cachet::settings.manage_theme.theme_mode.modes.light'),
            self::dark => __('cachet::settings.manage_theme.theme_mode.modes.dark'),
        };
    }
}
