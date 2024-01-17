<?php

namespace Cachet\Http\Controllers\StatusPage;

use Cachet\Cachet;
use Cachet\Models\ComponentGroup;
use Cachet\Models\Incident;
use Illuminate\View\View;

class StatusPageController
{
    /**
     * Show the status page.
     */
    public function index(): View
    {
        return view('cachet::status-page.index', [
            'componentGroups' => ComponentGroup::with('components')->get(),
            'cachetVersion' => Cachet::version(),
        ]);
    }

    /**
     * Show the details of a particular incident.
     */
    public function show(Incident $incident): View
    {
        return view('cachet::status-page.incident', [
            'incident' => $incident,
        ]);
    }
}
