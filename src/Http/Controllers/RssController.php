<?php

namespace Cachet\Http\Controllers;

use Cachet\Models\Incident;
use Cachet\Settings\AppSettings;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class RssController
{
    /**
     * The maximum number of incidents included in the feed.
     */
    private const MAX_ITEMS = 100;

    /**
     * How long the rendered feed is cached for, in seconds.
     */
    private const CACHE_TTL = 60;

    /**
     * Returns the RSS feed of all incidents.
     */
    public function __invoke(AppSettings $appSettings): Response
    {
        $feed = Cache::remember('cachet::rss-feed', self::CACHE_TTL, function () use ($appSettings) {
            return view('cachet::rss', [
                'statusPageName' => $appSettings->name,
                'statusAbout' => $appSettings->about,
                'incidents' => Incident::query()
                    ->guests()
                    ->with('updates')
                    ->when($appSettings->recent_incidents_only, function ($query) use ($appSettings) {
                        $query->where(function ($query) use ($appSettings) {
                            $query->whereDate(
                                'occurred_at',
                                '>',
                                Carbon::now()->subDays($appSettings->recent_incidents_days)->format('Y-m-d')
                            )->orWhere(function ($query) use ($appSettings) {
                                $query->whereNull('occurred_at')->whereDate(
                                    'created_at',
                                    '>',
                                    Carbon::now()->subDays($appSettings->recent_incidents_days)->format('Y-m-d')
                                );
                            });
                        });
                    })
                    ->orderByDesc('created_at')
                    ->limit(self::MAX_ITEMS)
                    ->get(),
            ])->render();
        });

        return response($feed)->header('Content-Type', 'application/rss+xml');
    }
}
