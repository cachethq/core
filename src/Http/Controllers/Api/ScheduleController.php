<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Actions\Schedule\CreateSchedule;
use Cachet\Actions\Schedule\DeleteSchedule;
use Cachet\Actions\Schedule\UpdateSchedule;
use Cachet\Http\Requests\CreateScheduleRequest;
use Cachet\Http\Requests\UpdateScheduleRequest;
use Cachet\Http\Resources\Schedule as ScheduleResource;
use Cachet\Models\Schedule;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Spatie\QueryBuilder\QueryBuilder;

class ScheduleController extends Controller
{
    /**
     * List Schedules.
     */
    public function index()
    {
        $schedules = QueryBuilder::for(Schedule::class)
            ->allowedIncludes(['components'])
            ->allowedFilters(['name', 'status'])
            ->allowedSorts(['name', 'status', 'id', 'scheduled_at', 'completed_at'])
            ->simplePaginate(request('per_page', 15));

        return ScheduleResource::collection($schedules);
    }

    /**
     * Create Schedule.
     */
    public function store(CreateScheduleRequest $request, CreateSchedule $createScheduleAction)
    {
        [$data, $components] = [$request->except('components'), $request->input('components')];

        $schedule = $createScheduleAction->handle($data, $components);

        return ScheduleResource::make($schedule);
    }

    /**
     * Get Schedule.
     */
    public function show(Schedule $schedule)
    {
        return ScheduleResource::make($schedule)
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update Schedule.
     */
    public function update(UpdateScheduleRequest $request, Schedule $schedule, UpdateSchedule $updateScheduleAction)
    {
        [$data, $components] = [$request->except('components'), $request->input('components')];
        $updateScheduleAction->handle($schedule, $data, $components);

        return ScheduleResource::make($schedule->fresh());
    }

    /**
     * Delete Schedule.
     */
    public function destroy(Schedule $schedule, DeleteSchedule $deleteScheduleAction)
    {
        $deleteScheduleAction->handle($schedule);

        return response()->noContent();
    }
}
