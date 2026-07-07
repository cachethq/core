<?php

namespace Tests\Feature\Filament;

use Cachet\Filament\Pages\EditProfile;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery;
use Filament\Facades\Filament;
use Workbench\App\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('cachet'));
});

it('registers app authentication as a multi-factor provider on the dashboard panel', function () {
    $providers = Filament::getPanel('cachet')->getMultiFactorAuthenticationProviders();

    expect($providers)->toHaveKey('app')
        ->and($providers['app'])->toBeInstanceOf(AppAuthentication::class)
        ->and($providers['app']->isRecoverable())->toBeTrue();
});

it('makes the user model support app authentication', function () {
    $user = User::factory()->create();

    expect($user)->toBeInstanceOf(HasAppAuthentication::class)
        ->and($user)->toBeInstanceOf(HasAppAuthenticationRecovery::class);
});

it('persists the app authentication secret and recovery codes', function () {
    $user = User::factory()->create();

    $user->saveAppAuthenticationSecret('SECRETKEY1234567');
    $user->saveAppAuthenticationRecoveryCodes(['code-one', 'code-two']);

    $user->refresh();

    expect($user->getAppAuthenticationSecret())->toBe('SECRETKEY1234567')
        ->and($user->getAppAuthenticationRecoveryCodes())->toBe(['code-one', 'code-two']);
});

it('hides the app authentication secret from serialization', function () {
    $user = User::factory()->create();

    $user->saveAppAuthenticationSecret('SECRETKEY1234567');

    expect($user->toArray())
        ->not->toHaveKey('app_authentication_secret')
        ->not->toHaveKey('app_authentication_recovery_codes');
});

it('shows the multi-factor authentication management section on the profile page', function () {
    actingAs(User::factory()->create(['is_admin' => true]));

    livewire(EditProfile::class)
        ->assertOk()
        ->assertSee(__('filament-panels::auth/pages/edit-profile.multi_factor_authentication.label'));
});
