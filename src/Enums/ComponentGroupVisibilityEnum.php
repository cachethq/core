<?php

namespace Cachet\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ComponentGroupVisibilityEnum: int implements HasColor, HasIcon, HasLabel
{
    case expanded = 0;
    case collapsed = 1;
    case collapsed_unless_incident = 2;

    public function getLabel(): string
    {
        return match ($this) {
            self::expanded => __('cachet::component_group.visibility.expanded'),
            self::collapsed => __('cachet::component_group.visibility.collapsed'),
            self::collapsed_unless_incident => __('cachet::component_group.visibility.collapsed_unless_incident'),
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::expanded => 'heroicon-o-chevron-down',
            self::collapsed => 'heroicon-o-chevron-up',
            self::collapsed_unless_incident => 'heroicon-o-chevron-up-down',
        };
    }

    public function getColor(): string
    {
        return 'gray';
    }
}
