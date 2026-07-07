<?php

namespace Cachet\Filament\Pages;

use Cachet\Filament\Widgets\Components;
use Cachet\Filament\Widgets\Feed;
use Cachet\Filament\Widgets\OpenIncidents;
use Cachet\Filament\Widgets\Overview;
use Cachet\Filament\Widgets\Support;
use Cachet\Filament\Widgets\SystemHealth;
use Cachet\Filament\Widgets\UpcomingMaintenance;
use Filament\Widgets\Widget;

class Dashboard extends \Filament\Pages\Dashboard
{
    protected static string|\BackedEnum|null $navigationIcon = 'cachet-dashboard';

    /**
     * @return array<class-string<Widget>>
     */
    public function getWidgets(): array
    {
        return [
            SystemHealth::class,
            Overview::class,
            OpenIncidents::class,
            UpcomingMaintenance::class,
            Components::class,
            Feed::class,
            Support::class,
        ];
    }
}
