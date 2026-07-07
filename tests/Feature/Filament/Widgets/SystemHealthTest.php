<?php

namespace Tests\Feature\Filament\Widgets;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Filament\Widgets\SystemHealth;
use Cachet\Models\Component;

use function Pest\Livewire\livewire;

it('system health smoke test', function () {
    $component = livewire(SystemHealth::class);

    $component->assertSuccessful();
});

it('shows the empty state when no components are configured', function () {
    $component = livewire(SystemHealth::class);

    $component->assertSuccessful();

    $component->assertSee(__('cachet::dashboard.system_health.empty'));
    $component->assertSee(__('cachet::dashboard.system_health.add_component'));
});

it('shows an operational status when all components are operational', function () {
    Component::factory()->count(2)->create([
        'status' => ComponentStatusEnum::operational,
        'enabled' => true,
    ]);

    $component = livewire(SystemHealth::class);

    $component->assertSuccessful();

    $component->assertSee(__('cachet::system_status.operational'));
    $component->assertSee(__('cachet::component.status.operational'));
});

it('shows a major outage when enough components are down', function () {
    Component::factory()->count(3)->create([
        'status' => ComponentStatusEnum::major_outage,
        'enabled' => true,
    ]);

    Component::factory()->create([
        'status' => ComponentStatusEnum::operational,
        'enabled' => true,
    ]);

    $component = livewire(SystemHealth::class);

    $component->assertSuccessful();

    $component->assertSee(__('cachet::system_status.major_outage'));
});

it('ignores disabled components', function () {
    Component::factory()->create([
        'status' => ComponentStatusEnum::major_outage,
        'enabled' => false,
    ]);

    $component = livewire(SystemHealth::class);

    $component->assertSuccessful();

    $component->assertSee(__('cachet::dashboard.system_health.empty'));
});
