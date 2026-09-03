<?php

namespace Tests\Feature\Filament;

use Cachet\Filament\Forms\Components\Toggle as CachetToggle;
use Cachet\Filament\Pages\Dashboard;
use Cachet\Filament\Resources\Components\ComponentResource;
use Cachet\Filament\Resources\Incidents\IncidentResource;
use Cachet\Filament\Resources\Subscribers\SubscriberResource;
use Cachet\Filament\Tables\Columns\ToggleColumn as CachetToggleColumn;
use Cachet\Filament\Widgets\Components;
use Cachet\Filament\Widgets\Feed;
use Cachet\Filament\Widgets\OpenIncidents;
use Cachet\Filament\Widgets\Overview;
use Cachet\Filament\Widgets\Support;
use Cachet\Filament\Widgets\SystemHealth;
use Cachet\Filament\Widgets\UpcomingMaintenance;
use Cachet\Models\Incident;
use Filament\Facades\Filament;
use Filament\Forms\Components\Toggle;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ToggleColumn;
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

it('allows the sidebar to collapse on desktop', function () {
    $panel = Filament::getPanel('cachet');

    expect($panel->isSidebarCollapsibleOnDesktop())->toBeTrue()
        ->and($panel->isSidebarFullyCollapsibleOnDesktop())->toBeFalse();
});

it('adds consistent icons to form and table toggles', function () {
    $hostFormToggle = Toggle::make('enabled');
    $hostTableToggle = ToggleColumn::make('enabled');
    $cachetFormToggle = CachetToggle::make('enabled');
    $cachetTableToggle = CachetToggleColumn::make('enabled');

    expect($hostFormToggle->getOnIcon())->toBeNull()
        ->and($hostTableToggle->getOnIcon())->toBeNull()
        ->and($cachetFormToggle->getOnIcon())->toBe(Heroicon::Check)
        ->and($cachetFormToggle->getOffIcon())->toBe(Heroicon::XMark)
        ->and($cachetTableToggle->getOnIcon())->toBe(Heroicon::Check)
        ->and($cachetTableToggle->getOffIcon())->toBe(Heroicon::XMark);
});

it('uses the same system font stack as the status page', function () {
    expect(Filament::getPanel('cachet')->getFontFamily())
        ->toBe('ui-sans-serif, system-ui, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"');
});

it('plots all 30 days including days without activity', function () {
    $this->travelTo(now()->startOfDay());

    Incident::factory()->create(['created_at' => now()->subDays(29)]);
    Incident::factory()->count(2)->create(['created_at' => now()->subDays(2)]);

    $overview = new class extends Overview
    {
        public function dailyIncidentCounts(): array
        {
            return $this->dailyCounts('incidents');
        }
    };

    $dailyCounts = $overview->dailyIncidentCounts();

    expect($dailyCounts)->toHaveCount(30)
        ->and($dailyCounts[0])->toBe(1)
        ->and($dailyCounts[1])->toBe(0)
        ->and($dailyCounts[27])->toBe(2)
        ->and($dailyCounts[29])->toBe(0);
});

it('links overview stats to their relevant resources', function () {
    Filament::setCurrentPanel(Filament::getPanel('cachet'));

    $overview = new class extends Overview
    {
        public function stats(): array
        {
            return $this->getStats();
        }
    };

    $stats = $overview->stats();

    expect($stats[0]->getUrl())->toBe(IncidentResource::getUrl('index'))
        ->and($stats[1]->getUrl())->toBe(ComponentResource::getUrl('index'))
        ->and($stats[2]->getUrl())->toBe(SubscriberResource::getUrl('index'));
});
