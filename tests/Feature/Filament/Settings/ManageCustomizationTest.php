<?php

namespace Tests\Feature\Filament\Settings;

use Cachet\Filament\Pages\Settings\ManageCustomization;
use Filament\Facades\Filament;
use Workbench\App\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('cachet'));

    actingAs(User::factory()->create(['is_admin' => true]));
});

it('renders headings for each customization section', function () {
    livewire(ManageCustomization::class)
        ->assertSee(__('cachet::settings.manage_customization.html_section_title'))
        ->assertSee(__('cachet::settings.manage_customization.stylesheet_section_title'));
});
