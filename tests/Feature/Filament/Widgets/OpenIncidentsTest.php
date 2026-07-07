<?php

namespace Tests\Feature\Filament\Widgets;

use Cachet\Enums\IncidentStatusEnum;
use Cachet\Filament\Widgets\OpenIncidents;
use Cachet\Models\Incident;

use function Pest\Livewire\livewire;

it('open incidents smoke test', function () {
    $component = livewire(OpenIncidents::class);

    $component->assertSuccessful();
});

it('lists unresolved incidents', function () {
    Incident::factory()->create([
        'name' => 'API is unreachable',
        'status' => IncidentStatusEnum::investigating,
    ]);

    $component = livewire(OpenIncidents::class);

    $component->assertSuccessful();

    $component->assertSee('API is unreachable');
    $component->assertSee(__('cachet::incident.status.investigating'));
});

it('does not list fixed incidents', function () {
    Incident::factory()->create([
        'name' => 'Resolved incident',
        'status' => IncidentStatusEnum::fixed,
    ]);

    $component = livewire(OpenIncidents::class);

    $component->assertSuccessful();

    $component->assertDontSee('Resolved incident');
});

it('shows the empty state when there are no open incidents', function () {
    $component = livewire(OpenIncidents::class);

    $component->assertSuccessful();

    $component->assertSee(__('cachet::dashboard.open_incidents.empty_state.heading'));
});
