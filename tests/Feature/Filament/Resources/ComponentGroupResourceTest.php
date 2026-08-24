<?php

namespace Tests\Feature\Filament\Resources;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Enums\ComponentStatusSourceEnum;
use Cachet\Enums\ResourceOrderColumnEnum;
use Cachet\Filament\Resources\ComponentGroups\Pages\CreateComponentGroup;
use Cachet\Filament\Resources\ComponentGroups\Pages\EditComponentGroup;
use Cachet\Filament\Resources\Components\RelationManagers\ComponentsRelationManager;
use Cachet\Models\Component;
use Cachet\Models\ComponentGroup;
use Filament\Facades\Filament;
use Workbench\App\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('cachet'));

    actingAs(User::factory()->create(['is_admin' => true]));
});

it('only shows an order direction when the selected order requires one', function () {
    livewire(CreateComponentGroup::class)
        ->assertSchemaComponentHidden('order_direction')
        ->fillForm(['order_column' => ResourceOrderColumnEnum::Name->value])
        ->assertSchemaComponentVisible('order_direction');
});

it('records component status changes made from a component group', function () {
    $componentGroup = ComponentGroup::factory()->create();
    $component = Component::factory()->for($componentGroup, 'group')->create([
        'status' => ComponentStatusEnum::operational,
    ]);

    livewire(ComponentsRelationManager::class, [
        'ownerRecord' => $componentGroup,
        'pageClass' => EditComponentGroup::class,
    ])
        ->callTableAction('edit', $component, [
            'name' => $component->name,
            'status' => ComponentStatusEnum::major_outage->value,
        ])
        ->assertHasNoTableActionErrors();

    $change = $component->statusChanges()->sole();

    expect($component->fresh()->status)->toBe(ComponentStatusEnum::major_outage)
        ->and($change->source)->toBe(ComponentStatusSourceEnum::Manual)
        ->and($change->causer->is(auth()->user()))->toBeTrue();
});
