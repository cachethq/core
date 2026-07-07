<?php

namespace Tests\Feature\Filament\Widgets;

use Cachet\Filament\Widgets\Feed;
use Illuminate\Support\Facades\Http;

use function Pest\Livewire\livewire;

it('feed smoke test', function () {
    Http::fake();

    $component = livewire(Feed::class);

    $component->assertSuccessful();
});

it('renders posts from the feed', function () {
    Http::fake([
        '*' => Http::response(<<<'XML'
            <?xml version="1.0" encoding="UTF-8"?>
            <rss version="2.0">
                <channel>
                    <title>Cachet Blog</title>
                    <item>
                        <title>Cachet 3.x released</title>
                        <link>https://blog.cachethq.io/cachet-3-x-released</link>
                        <description>The next major version of Cachet.</description>
                        <pubDate>Mon, 01 Jun 2026 09:00:00 +0000</pubDate>
                    </item>
                </channel>
            </rss>
            XML),
    ]);

    $component = livewire(Feed::class);

    $component->assertSuccessful();

    $component->assertSee('Cachet 3.x released');
});

it('renders the empty state when the feed cannot be fetched', function () {
    Http::fake([
        '*' => Http::response('not xml', 500),
    ]);

    $component = livewire(Feed::class);

    $component->assertSuccessful();

    $component->assertSee('No blog posts were found.');
});
