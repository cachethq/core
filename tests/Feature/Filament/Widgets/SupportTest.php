<?php

namespace Tests\Feature\Filament\Widgets;

use Cachet\Filament\Widgets\Support;

use function Pest\Livewire\livewire;

it('support smoke test', function () {
    $component = livewire(Support::class);

    $component->assertSuccessful();
});

it('renders the support section', function () {
    $component = livewire(Support::class);

    $component->assertSuccessful();

    $component->assertSee(__('cachet::cachet.support.section_heading'));
    $component->assertSee('GitHub Sponsors');
    $component->assertSee('Cachet blog');
});

it('links to the sponsorship page and the blog', function () {
    $component = livewire(Support::class);

    $component->assertSuccessful();

    $component->assertSeeHtml('href="https://github.com/cachethq/cachet/?sponsor=1"');
    $component->assertSeeHtml('href="https://blog.cachethq.io"');
});
