<?php

namespace Tests\Feature\Filament\Settings;

use Cachet\Filament\Pages\Settings\ManageLocalization;
use Cachet\Settings\AppSettings;
use Filament\Facades\Filament;
use Workbench\App\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('cachet'));

    actingAs(User::factory()->create(['is_admin' => true]));
});

it('renders the manage localization page', function () {
    $this->get(ManageLocalization::getUrl())->assertOk();
});

it('saves a named timezone', function () {
    expect(app(AppSettings::class)->timezone)->toBe('UTC');

    livewire(ManageLocalization::class)
        ->fillForm(['timezone' => 'Europe/Berlin'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(app(AppSettings::class)->refresh()->timezone)->toBe('Europe/Berlin');
});

it('saves the browser default timezone sentinel', function () {
    livewire(ManageLocalization::class)
        ->fillForm(['timezone' => '-'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(app(AppSettings::class)->refresh()->timezone)->toBe('-');
});
