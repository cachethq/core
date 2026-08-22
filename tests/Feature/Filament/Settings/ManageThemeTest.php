<?php

namespace Tests\Feature\Filament\Settings;

use Cachet\Filament\Pages\Settings\ManageTheme;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Workbench\App\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('cachet'));

    actingAs(User::factory()->create(['is_admin' => true]));
    Storage::fake('public');

    config()->set([
        'cachet.uploads.disk' => 'public',
        'cachet.uploads.max_size' => 256,
        'cachet.uploads.image_mime_types' => ['image/jpeg', 'image/png'],
    ]);
});

it('configures app logo upload validation', function () {
    livewire(ManageTheme::class)
        ->assertFormFieldExists('app_banner', function (FileUpload $field): bool {
            return $field->getDiskName() === 'public'
                && $field->getMaxSize() === 256
                && $field->getAcceptedFileTypes() === ['image/jpeg', 'image/png']
                && $field->shouldPreventFilePathTampering();
        });
});

it('rejects app logos with disallowed image types', function () {
    livewire(ManageTheme::class)
        ->fillForm([
            'app_banner' => UploadedFile::fake()->create('app-logo.gif', 32, 'image/gif'),
        ])
        ->call('save')
        ->assertHasFormErrors(['app_banner']);
});

it('rejects app logos larger than the configured maximum', function () {
    livewire(ManageTheme::class)
        ->fillForm([
            'app_banner' => UploadedFile::fake()->create('app-logo.png', 257, 'image/png'),
        ])
        ->call('save')
        ->assertHasFormErrors(['app_banner']);
});

it('accepts app logos within the configured constraints', function () {
    livewire(ManageTheme::class)
        ->fillForm([
            'app_banner' => UploadedFile::fake()->create('app-logo.png', 256, 'image/png'),
        ])
        ->call('save')
        ->assertHasNoFormErrors();
});
