<?php

namespace Tests\Feature\Filament;

use Cachet\Filament\Pages\Dashboard;
use Cachet\Filament\Widgets\Components;
use Cachet\Filament\Widgets\Feed;
use Cachet\Filament\Widgets\OpenIncidents;
use Cachet\Filament\Widgets\Overview;
use Cachet\Filament\Widgets\Support;
use Cachet\Filament\Widgets\SystemHealth;
use Cachet\Filament\Widgets\UpcomingMaintenance;
use Illuminate\Support\Facades\Http;
use Workbench\App\User;

use function Pest\Laravel\actingAs;
use function PHPUnit\Framework\assertSame;

it('renders the dashboard', function () {
    Http::fake();

    actingAs(User::factory()->create(['is_admin' => true]))
        ->get(Dashboard::getUrl())
        ->assertOk();
});

it('orders the widgets by importance', function () {
    assertSame([
        SystemHealth::class,
        Overview::class,
        OpenIncidents::class,
        UpcomingMaintenance::class,
        Components::class,
        Feed::class,
        Support::class,
    ], (new Dashboard)->getWidgets());
});
