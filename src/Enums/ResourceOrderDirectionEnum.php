<?php

namespace Cachet\Enums;

use Filament\Support\Contracts\HasLabel;

enum ResourceOrderDirectionEnum: string implements HasLabel
{
    case Asc = 'asc';
    case Desc = 'desc';

    public function getLabel(): string
    {
        return match ($this) {
            self::Asc => __('cachet::resource.order_direction.asc'),
            self::Desc => __('cachet::resource.order_direction.desc'),
        };
    }

    public function ascending(): bool
    {
        return $this === self::Asc;
    }

    public function descending(): bool
    {
        return $this === self::Desc;
    }
}
