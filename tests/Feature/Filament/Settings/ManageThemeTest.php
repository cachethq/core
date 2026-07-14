<?php

namespace Tests\Feature\Filament\Settings;

use Cachet\Enums\ThemeModeEnum;
use Cachet\Filament\Pages\Settings\ManageTheme;
use Cachet\Settings\ThemeSettings;
use Filament\Facades\Filament;
use Workbench\App\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('cachet'));

    actingAs(User::factory()->create(['is_admin' => true]));
});

it('renders the manage theme page', function () {
    $this->get(ManageTheme::getUrl())->assertOk();
});

it('saves the theme mode setting', function (ThemeModeEnum $mode) {
    expect(app(ThemeSettings::class)->theme_mode)->toBe(ThemeModeEnum::auto);

    livewire(ManageTheme::class)
        ->fillForm(['theme_mode' => $mode->value])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(app(ThemeSettings::class)->refresh()->theme_mode)->toBe($mode);
})->with([ThemeModeEnum::light, ThemeModeEnum::dark, ThemeModeEnum::auto]);
