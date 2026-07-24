<?php

namespace Cachet\Http\Controllers\StatusPage;

use Cachet\Models\Incident;
use Cachet\Models\Schedule;
use Cachet\Settings\AppSettings;
use Illuminate\View\View;

class StatusPageController
{
    /**
     * Create a new controller instance.
     */
    public function __construct(protected AppSettings $appSettings)
    {
        //
    }

    /**
     * Show the status page.
     */
    public function index(): View
    {
        return view('cachet::status-page.index', [
            'schedules' => Schedule::query()->with(['updates', 'components'])->published()->incomplete()->orderBy('scheduled_at')->get(),

            'display_graphs' => $this->appSettings->display_graphs,
        ]);
    }

    /**
     * Show the details of a particular incident.
     */
    public function show(Incident $incident): View
    {
        abort_if(! $incident->isPublished() && ! auth()->check(), 404);

        return view('cachet::status-page.incident', [
            'incident' => $incident->loadMissing([
                'components',
                'updates' => fn ($query) => $query->orderByDesc('created_at')->orderByDesc('id'),
            ]),
        ]);
    }

    /**
     * Show the details of a particular schedule.
     */
    public function schedule(Schedule $schedule): View
    {
        abort_if(! $schedule->isPublished() && ! auth()->check(), 404);

        return view('cachet::status-page.schedule', [
            'schedule' => $schedule->loadMissing([
                'components',
                'updates' => fn ($query) => $query->orderByDesc('created_at'),
            ]),
        ]);
    }
}
