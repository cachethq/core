<?php

namespace Tests\Feature\Filament\Resources;

use Cachet\Filament\Resources\Schedules\Pages\EditSchedule;
use Cachet\Models\Schedule;
use Filament\Facades\Filament;
use Workbench\App\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('cachet'));

    actingAs(User::factory()->create(['is_admin' => true]));
});

it('records a schedule update from the edit page header action', function () {
    $schedule = Schedule::factory()->create(['scheduled_at' => now()->subHour()]);

    livewire(EditSchedule::class, ['record' => $schedule->getKey()])
        ->callAction('add-update', ['message' => 'Maintenance is progressing.'])
        ->assertHasNoActionErrors();

    expect($schedule->fresh()->updates)->toHaveCount(1)
        ->and($schedule->updates->sole()->message)->toBe('Maintenance is progressing.');
});
