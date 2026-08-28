<?php

namespace Tests\Feature\Filament\Resources;

use Cachet\Filament\Resources\ApiKeys\Pages\CreateApiKey;
use Filament\Facades\Filament;
use Filament\Forms\Components\CheckboxList;
use Illuminate\Auth\Access\AuthorizationException;
use Workbench\App\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('cachet'));
});

it('allows administrators to create full access tokens', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    actingAs($admin);

    (new CreateApiKey)->handleRecordCreation([
        'name' => 'Full access',
        'abilities' => [],
        'expires_at' => null,
    ]);

    expect($admin->tokens()->sole()->abilities)->toBe(['*']);
});

it('prevents non-administrators from creating full access tokens', function () {
    $user = User::factory()->create(['is_admin' => false]);

    actingAs($user);

    expect(fn () => (new CreateApiKey)->handleRecordCreation([
        'name' => 'Full access',
        'abilities' => [],
        'expires_at' => null,
    ]))->toThrow(AuthorizationException::class);

    expect($user->tokens()->exists())->toBeFalse();
});

it('prevents non-administrators from submitting a wildcard ability', function () {
    $user = User::factory()->create(['is_admin' => false]);

    actingAs($user);

    expect(fn () => (new CreateApiKey)->handleRecordCreation([
        'name' => 'Submitted wildcard',
        'abilities' => ['*'],
        'expires_at' => null,
    ]))->toThrow(AuthorizationException::class);

    expect($user->tokens()->exists())->toBeFalse();
});

it('prevents non-administrators from creating subscriber tokens', function () {
    $user = User::factory()->create(['is_admin' => false]);

    actingAs($user);

    expect(fn () => (new CreateApiKey)->handleRecordCreation([
        'name' => 'Subscriber access',
        'abilities' => ['subscribers.manage'],
        'expires_at' => null,
    ]))->toThrow(AuthorizationException::class);

    expect($user->tokens()->exists())->toBeFalse();
});

it('allows non-administrators to create scoped operational tokens', function () {
    $user = User::factory()->create(['is_admin' => false]);

    actingAs($user);

    (new CreateApiKey)->handleRecordCreation([
        'name' => 'Incident access',
        'abilities' => ['incidents.manage'],
        'expires_at' => null,
    ]);

    expect($user->tokens()->sole()->abilities)->toBe(['incidents.manage']);
});

it('hides subscriber abilities from non-administrators', function () {
    actingAs(User::factory()->create(['is_admin' => false]));

    livewire(CreateApiKey::class)
        ->assertFormFieldExists('abilities', fn (CheckboxList $field): bool => ! array_key_exists(
            'subscribers.manage',
            $field->getOptions(),
        ));
});
