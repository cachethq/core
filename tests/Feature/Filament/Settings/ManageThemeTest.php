<?php

namespace Tests\Feature\Filament\Settings;

use Cachet\Filament\Pages\Settings\ManageTheme;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Workbench\App\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('cachet'));

    actingAs(User::factory()->create(['is_admin' => true]));
});

it('hides the duplicate banner field label beneath the section heading', function () {
    livewire(ManageTheme::class)
        ->assertSchemaComponentExists(
            'app_banner',
            'form',
            fn (FileUpload $field): bool => $field->isLabelHidden()
                && $field->getLabel() === __('cachet::settings.manage_theme.app_banner_label'),
        );
});
