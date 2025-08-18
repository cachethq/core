<?php

namespace Cachet\Filament\Widgets;

use Filament\Widgets\Concerns\CanPoll;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Uri;
use Throwable;

class Feed extends Widget
{
    use CanPoll;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'cachet::filament.widgets.feed';

    protected static ?int $sort = 10;

    protected function getViewData(): array
    {
        return [
            'items' => $this->getFeed(),
            'noItems' => Blade::render($this->getEmptyBlock()),
        ];
    }

    /**
     * Get the generated empty block text.
     */
    public function getEmptyBlock(): string
    {
        return preg_replace(
            '/\*(.*?)\*/',
            '<x-filament::link href="'.config('cachet.feed.uri').'" target="_blank" rel="nofollow noopener">$1</x-filament::link>',
            __('cachet::cachet.feed.empty')
        );
    }

    /**
     * Get the feed from the cache or fetch it fresh.
     */
    protected function getFeed(): array
    {
        return Cache::flexible('cachet-feed', [
            60 * 15,
            60 * 60,
        ], fn () => $this->fetchFeed(
            config('cachet.feed.uri')
        ));
    }

    /**
     * Fetch the data from the given RSS feed.
     */
    protected function fetchFeed(string $uri, int $maxPosts = 5): array
    {
        try {
            $response = Http::get($uri);

            $xml = simplexml_load_string($response->getBody());

            $posts = [];

            $feedItems = $xml->channel->item ?? $xml->entry ?? [];
            $feedIndex = 0;

            foreach ($feedItems as $item) {
                if ($feedIndex >= $maxPosts) {
                    break;
                }

                $posts[] = [
                    'title' => (string) ($item->title ?? ''),
                    'link' => Uri::of((string) ($item->link ?? ''))->withQuery([
                        'utm_source' => 'cachet',
                        'utm_medium' => 'installation',
                        'utm_campaign' => 'dashboard',
                    ]),
                    'description' => Str::of($item->description ?? $item->summary ?? '')->limit(preserveWords: true),
                    'date' => Carbon::parse((string) ($item->pubDate ?? $item->updated ?? '')),
                ];

                $feedIndex++;
            }

            return $posts;
        } catch (Throwable $e) {
            return [];
        }
    }
}
