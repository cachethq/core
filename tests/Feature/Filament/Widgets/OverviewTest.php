<?php

namespace Tests\Feature\Filament\Widgets;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\IncidentStatusEnum;
use Cachet\Filament\Widgets\Overview;
use Cachet\Models\Component;
use Cachet\Models\Incident;
use Cachet\Models\Subscriber;

use function Pest\Livewire\livewire;

it('overview smoke test', function () {
    $component = livewire(Overview::class);

    $component->assertSuccessful();
});

it('counts only unresolved incidents', function () {
    Incident::factory()->count(2)->create([
        'status' => IncidentStatusEnum::investigating,
    ]);

    Incident::factory()->create([
        'status' => IncidentStatusEnum::fixed,
    ]);

    $component = livewire(Overview::class);

    $component->assertSuccessful();

    $component->assertSee(__('cachet::incident.overview.open_incidents_label'));
    $component->assertSee('2');
});

it('shows operational components out of the enabled total', function () {
    Component::factory()->count(3)->create([
        'status' => ComponentStatusEnum::operational,
        'enabled' => true,
    ]);

    Component::factory()->create([
        'status' => ComponentStatusEnum::major_outage,
        'enabled' => true,
    ]);

    Component::factory()->create([
        'status' => ComponentStatusEnum::operational,
        'enabled' => false,
    ]);

    $component = livewire(Overview::class);

    $component->assertSuccessful();

    $component->assertSee('3 / 4');
});

it('shows the total and verified subscriber counts', function () {
    Subscriber::factory()->count(2)->verified()->create();
    Subscriber::factory()->create();

    $component = livewire(Overview::class);

    $component->assertSuccessful();

    $component->assertSee(__('cachet::subscriber.overview.total_subscribers_label'));
    $component->assertSee('3');
    $component->assertSee(__('cachet::subscriber.overview.verified_subscribers_description', ['count' => 2]));
});
