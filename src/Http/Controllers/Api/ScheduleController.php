<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Actions\Schedule\CreateSchedule;
use Cachet\Actions\Schedule\DeleteSchedule;
use Cachet\Actions\Schedule\UpdateSchedule;
use Cachet\Data\Schedule\CreateScheduleData;
use Cachet\Data\Schedule\UpdateScheduleData;
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
    public function store(CreateScheduleData $data, CreateSchedule $createScheduleAction)
    {
        $schedule = $createScheduleAction->handle($data);

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
    public function update(UpdateScheduleData $data, Schedule $schedule, UpdateSchedule $updateScheduleAction)
    {
        $updateScheduleAction->handle($schedule, $data);

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
