<?php

namespace Cachet\Http\Controllers;

use Cachet\Models\Incident;
use Cachet\Settings\AppSettings;
use Illuminate\Http\Response;

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
                ->with('incidentUpdates')
                ->orderByDesc('created_at')
                ->get(),
        ])->header('Content-Type', 'application/rss+xml');
    }
}
