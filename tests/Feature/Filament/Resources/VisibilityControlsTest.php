<?php

namespace Tests\Feature\Filament\Resources;

use Cachet\Filament\Resources\ComponentGroups\Pages\CreateComponentGroup;
use Cachet\Filament\Resources\Incidents\Pages\CreateIncident;
use Cachet\Filament\Resources\Metrics\Pages\CreateMetric;
use Filament\Facades\Filament;
use Filament\Forms\Components\ToggleButtons;
use Filament\Support\Enums\GridDirection;
use Workbench\App\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('cachet'));

    actingAs(User::factory()->create(['is_admin' => true]));
});

it('lays out visibility controls without overflowing their sidebar', function (string $pageClass) {
    livewire($pageClass)
        ->assertSchemaComponentExists(
            'visible',
            'form',
            fn (ToggleButtons $field): bool => ! $field->isInline()
                && $field->getColumns() === [
                    'default' => 1,
                    'sm' => 3,
                    'lg' => 1,
                ]
                && $field->getGridDirection() === GridDirection::Row,
        );
})->with([
    CreateComponentGroup::class,
    CreateIncident::class,
    CreateMetric::class,
]);
