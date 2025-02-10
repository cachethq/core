<?php

namespace Cachet\Http\Controllers\StatusPage;

use Cachet\Models\Component;
use Cachet\Models\ComponentGroup;
use Cachet\Models\Incident;
use Cachet\Models\Schedule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class StatusPageController
{
    /**
     * Show the status page.
     */
    public function index(): View
    {
        return view('cachet::status-page.index', [
            'componentGroups' => ComponentGroup::query()
                ->with(['components' => fn ($query) => $query->enabled()->orderBy('order')->withCount('incidents')])
                ->visible(auth()->check())
                ->orderBy('order')
                ->when(auth()->check(), fn (Builder $query) => $query->users(), fn ($query) => $query->guests())
                ->get(),
            'ungroupedComponents' => Component::query()
                ->enabled()
                ->whereNull('component_group_id')
                ->orderBy('order')
                ->withCount('incidents')
                ->get(),

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
