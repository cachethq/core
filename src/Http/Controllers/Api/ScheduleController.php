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

/**
 * @group Schedules
 */
class ScheduleController extends Controller
{
    /**
     * List Schedules
     *
     * @apiResourceCollection \Cachet\Http\Resources\Schedule
     * @apiResourceModel \Cachet\Models\Schedule
     */
    public function index()
    {
        $schedules = QueryBuilder::for(Schedule::class)
            ->allowedIncludes(['components'])
            ->allowedFilters(['name'])
            ->allowedSorts(['name', 'id', 'scheduled_at', 'completed_at'])
            ->simplePaginate(request('per_page', 15));

        return ScheduleResource::collection($schedules);
    }

    /**
     * Create Schedule
     *
     * @apiResource \Cachet\Http\Resources\Schedule
     * @apiResourceModel \Cachet\Models\Schedule
     * @authenticated
     */
    public function store(CreateScheduleRequest $request, CreateSchedule $createScheduleAction)
    {
        [$data, $components] = [$request->except('components'), $request->input('components')];

        $schedule = $createScheduleAction->handle($data, $components);

        return ScheduleResource::make($schedule);
    }

    /**
     * Get Schedule
     *
     * @apiResource \Cachet\Http\Resources\Schedule
     * @apiResourceModel \Cachet\Models\Schedule
     */
    public function show(Schedule $schedule)
    {
        return ScheduleResource::make($schedule)
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update Schedule
     *
     * @apiResource \Cachet\Http\Resources\Schedule
     * @apiResourceModel \Cachet\Models\Schedule
     * @authenticated
     */
    public function update(UpdateScheduleRequest $request, Schedule $schedule, UpdateSchedule $updateScheduleAction)
    {
        [$data, $components] = [$request->except('components'), $request->input('components')];
        $updateScheduleAction->handle($schedule, $data, $components);

        return ScheduleResource::make($schedule->fresh());
    }

    /**
     * Delete Schedule
     *
     * @response 204
     * @authenticated
     */
    public function destroy(Schedule $schedule, DeleteSchedule $deleteScheduleAction)
    {
        $deleteScheduleAction->handle($schedule);

        return response()->noContent();
    }
}
