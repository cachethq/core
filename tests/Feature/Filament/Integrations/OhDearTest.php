<?php

namespace Tests\Feature\Filament\Integrations;

use Cachet\Filament\Pages\Integrations\OhDear;
use Filament\Facades\Filament;
use Workbench\App\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('cachet'));

    actingAs(User::factory()->create(['is_admin' => true]));
});

it('renders headings for each import section', function () {
    livewire(OhDear::class)
        ->assertSee(__('cachet::integrations.oh_dear.status_page_section_title'))
        ->assertSee(__('cachet::integrations.oh_dear.import_options_section_title'));
});
