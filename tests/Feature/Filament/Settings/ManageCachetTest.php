<?php

namespace Tests\Feature\Filament\Settings;

use Cachet\Filament\Pages\Settings\ManageCachet;
use Cachet\Settings\AppSettings;
use Filament\Facades\Filament;
use Workbench\App\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('cachet'));

    actingAs(User::factory()->create(['is_admin' => true]));
});

it('renders the manage cachet page', function () {
    $this->get(ManageCachet::getUrl())->assertOk();
});

it('saves the dynamic favicon setting', function () {
    expect(app(AppSettings::class)->dynamic_favicon)->toBeFalse();

    livewire(ManageCachet::class)
        ->fillForm(['dynamic_favicon' => true])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(app(AppSettings::class)->refresh()->dynamic_favicon)->toBeTrue();
});
