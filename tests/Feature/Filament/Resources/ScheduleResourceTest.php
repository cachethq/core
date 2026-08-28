<?php

namespace Tests\Feature\Filament\Resources;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Filament\Components\ComponentOptions;
use Cachet\Filament\Resources\Schedules\Pages\CreateSchedule;
use Cachet\Filament\Resources\Schedules\Pages\EditSchedule;
use Cachet\Filament\Resources\Schedules\Pages\ListSchedules;
use Cachet\Models\Component;
use Cachet\Models\ComponentGroup;
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

it('excludes already attached components from the schedule attach action options', function () {
    $schedule = Schedule::factory()->create();
    $attached = Component::factory()->create(['name' => 'Already Attached']);

    $schedule->components()->attach($attached->id, ['component_status' => ComponentStatusEnum::operational->value]);

    $options = ComponentOptions::forSelect($schedule);

    expect(collect($options)->flatten()->all())->not->toContain('Already Attached');
});

it('groups schedule component options by component group', function () {
    $firstGroup = ComponentGroup::factory()->create(['name' => 'API Services', 'order' => 1]);
    Component::factory()->for($firstGroup, 'group')->create(['name' => 'API']);
    Component::factory()->create(['name' => 'Standalone Service']);

    $options = ComponentOptions::forSelect();

    expect($options)
        ->toHaveKey('API Services')
        ->toHaveKey(__('cachet::component.list.ungrouped'))
        ->and($options['API Services'])->toContain('API')
        ->and($options[__('cachet::component.list.ungrouped')])->toContain('Standalone Service');
});

it('groups component selections by component group when creating a schedule', function () {
    $firstGroup = ComponentGroup::factory()->create(['name' => 'API Services', 'order' => 1]);
    $secondGroup = ComponentGroup::factory()->create(['name' => 'Web Services', 'order' => 2]);

    Component::factory()->for($firstGroup, 'group')->create(['name' => 'API']);
    Component::factory()->for($secondGroup, 'group')->create(['name' => 'Web']);
    Component::factory()->create(['name' => 'Standalone Service']);

    livewire(CreateSchedule::class)
        ->fillForm([
            'scheduleComponents' => [['component_id' => null]],
        ])
        ->assertSeeInOrder(['API Services', 'Web Services', __('cachet::component.list.ungrouped')])
        ->assertSee('API')
        ->assertSee('Web')
        ->assertSee('Standalone Service');
});
