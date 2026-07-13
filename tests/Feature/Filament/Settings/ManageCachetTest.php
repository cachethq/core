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

it('saves the mcp settings', function () {
    expect(app(AppSettings::class))
        ->mcp_enabled->toBeFalse()
        ->mcp_protected->toBeTrue();

    livewire(ManageCachet::class)
        ->fillForm(['mcp_enabled' => true, 'mcp_protected' => false])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(app(AppSettings::class)->refresh())
        ->mcp_enabled->toBeTrue()
        ->mcp_protected->toBeFalse();
});
