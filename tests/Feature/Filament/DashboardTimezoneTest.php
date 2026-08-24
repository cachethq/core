<?php

namespace Tests\Feature\Filament;

use Cachet\Filament\Resources\Incidents\Pages\EditIncident;
use Cachet\Models\Incident;
use Cachet\Settings\AppSettings;
use Filament\Facades\Filament;
use Workbench\App\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('cachet'));
    Filament::bootCurrentPanel();

    actingAs(User::factory()->create(['is_admin' => true]));
});

it('renders dashboard date times in the configured display timezone', function () {
    $settings = app(AppSettings::class);
    $settings->timezone = 'Europe/Berlin';
    $settings->save();

    $incident = Incident::factory()->create(['occurred_at' => '2026-08-06 13:01:37']);

    livewire(EditIncident::class, ['record' => $incident->getKey()])
        ->assertSet('data.occurred_at', '2026-08-06 15:01:37');
});

it('stores dashboard-edited date times in the application timezone', function () {
    $settings = app(AppSettings::class);
    $settings->timezone = 'Europe/Berlin';
    $settings->save();

    $incident = Incident::factory()->create();

    livewire(EditIncident::class, ['record' => $incident->getKey()])
        ->fillForm(['occurred_at' => '2026-08-06 15:01:37'])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('incidents', [
        'id' => $incident->id,
        'occurred_at' => '2026-08-06 13:01:37',
    ]);
});

it('falls back to the application timezone when the browser default sentinel is configured', function () {
    $settings = app(AppSettings::class);
    $settings->timezone = '-';
    $settings->save();

    $incident = Incident::factory()->create(['occurred_at' => '2026-08-06 13:01:37']);

    livewire(EditIncident::class, ['record' => $incident->getKey()])
        ->assertSet('data.occurred_at', '2026-08-06 13:01:37');
});
