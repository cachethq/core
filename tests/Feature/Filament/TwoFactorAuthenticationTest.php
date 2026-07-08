<?php

namespace Tests\Feature\Filament;

use Cachet\Filament\MultiFactor\AppAuthentication;
use Cachet\Filament\Pages\EditProfile;
use Cachet\Filament\Resources\Users\Pages\ListUsers;
use Cachet\Settings\AppSettings;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\OneTimeCodeInput;
use Filament\Schemas\Components\Image;
use Filament\Schemas\Components\Wizard;
use Illuminate\Support\Facades\DB;
use Workbench\App\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('cachet'));

    actingAs(User::factory()->create(['is_admin' => true]));
});

it('registers app authentication on the panel', function () {
    $providers = Filament::getCurrentPanel()->getMultiFactorAuthenticationProviders();

    expect($providers)->toHaveCount(1)
        ->and($providers['app'])->toBeInstanceOf(AppAuthentication::class)
        ->and($providers['app']->isRecoverable())->toBeTrue();
});

it('renders the set up action as a button', function () {
    $provider = Filament::getCurrentPanel()->getMultiFactorAuthenticationProviders()['app'];

    $setUpAction = collect($provider->getActions())
        ->first(fn ($action) => $action->getName() === 'setUpAppAuthentication');

    expect($setUpAction->isButton())->toBeTrue();
});

it('mounts the set up wizard with the restyled first step', function () {
    $page = livewire(EditProfile::class)
        ->mountAction(TestAction::make('setUpAppAuthentication')->schemaComponent(schema: 'content'))
        ->instance();

    $wizard = $page->getSchema($page->getMountedActionSchemaName())->getComponents()[0];

    expect($wizard)->toBeInstanceOf(Wizard::class);

    $appStepComponents = $wizard->getChildComponents()[0]->getChildComponents();

    expect($appStepComponents[1])->toBeInstanceOf(OneTimeCodeInput::class);

    $image = collect($appStepComponents[0]->getChildComponents())
        ->first(fn (mixed $component): bool => $component instanceof Image);

    expect($image->getImageHeight())->toBe('10rem');
});

it('labels authenticator app entries with the status page name', function () {
    $provider = Filament::getCurrentPanel()->getMultiFactorAuthenticationProviders()['app'];

    $settings = app(AppSettings::class);
    $settings->name = 'Acme Status';
    $settings->save();

    expect($provider->getBrandName())->toBe('Acme Status');
});

it('shows the two factor authentication section on the profile page', function () {
    $this->get(EditProfile::getUrl())
        ->assertOk()
        ->assertSee(__('filament-panels::auth/pages/edit-profile.multi_factor_authentication.label'));
});

it('encrypts the app authentication secret and recovery codes', function () {
    $user = User::factory()->create();

    $user->saveAppAuthenticationSecret('super-secret');
    $user->saveAppAuthenticationRecoveryCodes(['code-one', 'code-two']);

    $rawUser = DB::table('users')->find($user->id);

    expect($rawUser->app_authentication_secret)->not->toContain('super-secret')
        ->and($rawUser->app_authentication_recovery_codes)->not->toContain('code-one')
        ->and($user->fresh()->getAppAuthenticationSecret())->toBe('super-secret')
        ->and($user->fresh()->getAppAuthenticationRecoveryCodes())->toBe(['code-one', 'code-two']);
});

it('hides the app authentication secret and recovery codes from serialization', function () {
    $user = User::factory()->create();

    $user->saveAppAuthenticationSecret('super-secret');
    $user->saveAppAuthenticationRecoveryCodes(['code-one']);

    expect($user->fresh()->toArray())
        ->not->toHaveKeys(['app_authentication_secret', 'app_authentication_recovery_codes']);
});

it('resets a user\'s two factor authentication through the table action', function () {
    $user = User::factory()->create();
    $user->saveAppAuthenticationSecret('super-secret');
    $user->saveAppAuthenticationRecoveryCodes(['code-one', 'code-two']);

    livewire(ListUsers::class)
        ->callAction(TestAction::make('reset-two-factor')->table($user))
        ->assertHasNoActionErrors();

    $user = $user->fresh();

    expect($user->getAppAuthenticationSecret())->toBeNull()
        ->and($user->getAppAuthenticationRecoveryCodes())->toBeNull();
});

it('hides the reset two factor action for users without two factor authentication', function () {
    $user = User::factory()->create();

    livewire(ListUsers::class)
        ->assertActionHidden(TestAction::make('reset-two-factor')->table($user));
});
