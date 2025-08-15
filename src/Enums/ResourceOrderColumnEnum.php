<?php

namespace Cachet\Enums;

use Filament\Support\Contracts\HasLabel;

enum ResourceOrderColumnEnum: string implements HasLabel
{
    case Id = 'id';
    case LastUpdated = 'last_updated';
    case Name = 'name';
    case Manual = 'manual';
    case Status = 'status';

    public function getLabel(): string
    {
        return match ($this) {
            self::Id => __('cachet::resource.order_column.id'),
            self::LastUpdated => __('cachet::resource.order_column.last_updated'),
            self::Name => __('cachet::resource.order_column.name'),
            self::Manual => __('cachet::resource.order_column.manual'),
            self::Status => __('cachet::resource.order_column.status'),
        };
    }

    /**
     * Determine if the column requires a direction.
     */
    public static function requiresDirection(): array
    {
        return [
            self::Id,
            self::LastUpdated,
            self::Name,
            self::Status,
        ];
    }
}
