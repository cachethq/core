<?php

namespace Tests\Feature\Filament\Resources;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Filament\Resources\Components\Pages\CreateComponent;
use Cachet\Filament\Resources\Components\Pages\EditComponent;
use Cachet\Models\Component;
use Filament\Facades\Filament;
use Workbench\App\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('cachet'));

    actingAs(User::factory()->create(['is_admin' => true]));
});

it('can create a component with tags', function () {
    livewire(CreateComponent::class)
        ->fillForm([
            'name' => 'API',
            'status' => ComponentStatusEnum::operational,
            'tags' => ['api', 'database'],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $component = Component::firstWhere('name', 'API');

    expect($component->tags->pluck('name')->all())->toEqualCanonicalizing(['api', 'database']);
});

it('can update a component\'s tags', function () {
    $component = Component::factory()->create();
    $component->attachTag('api');

    livewire(EditComponent::class, ['record' => $component->getKey()])
        ->fillForm([
            'tags' => ['database'],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($component->fresh()->tags->pluck('name')->all())->toBe(['database']);
});
