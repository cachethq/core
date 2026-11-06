<?php

namespace Tests\Feature\Filament\Integrations;

use Cachet\Filament\Pages\Integrations\OhDear;
use Filament\Facades\Filament;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
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

it('rejects private import URLs before sending a request', function () {
    Http::preventStrayRequests();

    livewire(OhDear::class)
        ->fillForm(['url' => 'http://169.254.169.254'])
        ->call('importFeed')
        ->assertHasFormErrors(['url']);

    Http::assertNothingSent();
});

it('imports from a validated public URL', function () {
    Http::preventStrayRequests();

    Http::fake([
        'https://93.184.216.34/json' => Http::response([
            'sites' => [],
            'summarizedStatus' => ['status' => 'up'],
        ]),
    ]);

    livewire(OhDear::class)
        ->fillForm([
            'url' => 'https://93.184.216.34',
            'import_sites' => false,
            'import_incidents' => false,
        ])
        ->call('importFeed')
        ->assertHasNoFormErrors();

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://93.184.216.34/json');
});
