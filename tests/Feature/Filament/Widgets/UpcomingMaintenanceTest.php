<?php

namespace Tests\Feature\Filament\Widgets;

use Cachet\Filament\Widgets\UpcomingMaintenance;
use Cachet\Models\Schedule;

use function Pest\Livewire\livewire;

it('upcoming maintenance smoke test', function () {
    $component = livewire(UpcomingMaintenance::class);

    $component->assertSuccessful();
});

it('lists upcoming and in-progress maintenance', function () {
    Schedule::factory()->create([
        'name' => 'Database upgrade',
        'scheduled_at' => now()->addDays(2),
        'completed_at' => null,
    ]);

    Schedule::factory()->inProgress()->create([
        'name' => 'Network maintenance',
    ]);

    $component = livewire(UpcomingMaintenance::class);

    $component->assertSuccessful();

    $component->assertSee('Database upgrade');
    $component->assertSee('Network maintenance');
});

it('does not list completed maintenance', function () {
    Schedule::factory()->completed()->create([
        'name' => 'Finished maintenance',
    ]);

    $component = livewire(UpcomingMaintenance::class);

    $component->assertSuccessful();

    $component->assertDontSee('Finished maintenance');
});

it('shows the empty state when there is no maintenance', function () {
    $component = livewire(UpcomingMaintenance::class);

    $component->assertSuccessful();

    $component->assertSee(__('cachet::dashboard.upcoming_maintenance.empty_state.heading'));
});
