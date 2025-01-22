<?php

namespace Cachet\Http\Controllers\StatusPage;

use Cachet\Enums\ResourceOrderColumnEnum;
use Cachet\Models\Component;
use Cachet\Models\ComponentGroup;
use Cachet\Models\Incident;
use Cachet\Models\Schedule;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StatusPageController
{
    /**
     * Show the status page.
     */
    public function index(): View
    {
        return view('cachet::status-page.index', [
            'schedules' => Schedule::query()->with('updates')->incomplete()->orderBy('scheduled_at')->get(),
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
