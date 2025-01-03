<?php

namespace Cachet\Http\Controllers;

use Cachet\Models\Incident;
use Cachet\Settings\AppSettings;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class RssController
{
    /**
     * Returns the RSS feed of all incidents.
     */
    public function __invoke(AppSettings $appSettings): Response
    {
        return response()->view('cachet::rss', [
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
                ->get(),
        ])->header('Content-Type', 'application/rss+xml');
    }
}
