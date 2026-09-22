<?php

namespace Tests\Feature\Filament\Resources;

use Cachet\Filament\Resources\Users\Pages\EditUser;
use Cachet\Filament\Resources\Users\Pages\ListUsers;
use Cachet\Filament\Resources\Users\UserResource;
use Filament\Facades\Filament;
use Workbench\App\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('cachet'));

    $this->admin = User::factory()->create(['is_admin' => true]);

    actingAs($this->admin);
});

it('can edit a user without changing their email', function () {
    $user = User::factory()->create(['email' => 'james@example.com']);

    livewire(EditUser::class, ['record' => $user->getRouteKey()])
        ->fillForm(['name' => 'Renamed User'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($user->fresh())
        ->name->toBe('Renamed User')
        ->email->toBe('james@example.com');
});

it('cannot change a user email to one that is already taken', function () {
    User::factory()->create(['email' => 'taken@example.com']);
    $user = User::factory()->create(['email' => 'james@example.com']);

    livewire(EditUser::class, ['record' => $user->getRouteKey()])
        ->fillForm(['email' => 'taken@example.com'])
        ->call('save')
        ->assertHasFormErrors(['email']);
});

it('prevents an administrator from deleting themselves', function () {
    expect(UserResource::canDelete($this->admin))->toBeFalse();
});

it('allows an administrator to delete another administrator', function () {
    $otherAdmin = User::factory()->create(['is_admin' => true]);

    expect(UserResource::canDelete($otherAdmin))->toBeTrue();
});

it('protects the current administrator during a bulk delete', function () {
    $otherAdmin = User::factory()->create(['is_admin' => true]);

    livewire(ListUsers::class)
        ->callTableBulkAction('delete', [$this->admin, $otherAdmin]);

    expect($this->admin->fresh())->not->toBeNull()
        ->and($otherAdmin->fresh())->toBeNull();
});

it('denies non-administrators direct access to users', function () {
    actingAs(User::factory()->create(['is_admin' => false]));

    $this->get(UserResource::getUrl('index'))
        ->assertForbidden();
});
