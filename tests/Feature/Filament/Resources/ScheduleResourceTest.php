<?php

namespace Tests\Feature\Filament\Resources;

use Cachet\Filament\Resources\Schedules\Pages\EditSchedule;
use Cachet\Filament\Resources\Schedules\Pages\ListSchedules;
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

it('sorts schedules by their computed status', function () {
    $complete = Schedule::factory()->create([
        'scheduled_at' => now()->subHours(2),
        'completed_at' => now()->subHour(),
    ]);
    $inProgress = Schedule::factory()->create([
        'scheduled_at' => now()->subHour(),
        'completed_at' => null,
    ]);
    $upcoming = Schedule::factory()->create([
        'scheduled_at' => now()->addHour(),
        'completed_at' => null,
    ]);

    livewire(ListSchedules::class)
        ->sortTable('status')
        ->assertCanSeeTableRecords([$upcoming, $inProgress, $complete], inOrder: true);
});
