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

it('renders headings for each settings section', function () {
    livewire(ManageCachet::class)
        ->assertSee(__('cachet::settings.manage_cachet.general_settings_title'))
        ->assertSee(__('cachet::settings.manage_cachet.incident_settings_title'));
});

it('saves the dynamic favicon setting', function () {
    expect(app(AppSettings::class)->dynamic_favicon)->toBeFalse();

    livewire(ManageCachet::class)
        ->fillForm(['dynamic_favicon' => true])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(app(AppSettings::class)->refresh()->dynamic_favicon)->toBeTrue();
});

it('saves the component group status visibility setting', function () {
    expect(app(AppSettings::class)->show_component_group_status)->toBeTrue();

    livewire(ManageCachet::class)
        ->fillForm(['show_component_group_status' => false])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(app(AppSettings::class)->refresh()->show_component_group_status)->toBeFalse();
});

it('saves the status page title and about visibility settings', function () {
    expect(app(AppSettings::class))
        ->show_site_name->toBeFalse()
        ->show_about->toBeTrue();

    livewire(ManageCachet::class)
        ->fillForm(['show_site_name' => true, 'show_about' => false])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(app(AppSettings::class)->refresh())
        ->show_site_name->toBeTrue()
        ->show_about->toBeFalse();
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
