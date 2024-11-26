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

/**
 * @group Schedules
 */
class ScheduleController extends Controller
{
    /**
     * List Schedules
     *
     * @apiResourceCollection \Cachet\Http\Resources\Schedule
     *
     * @apiResourceModel \Cachet\Models\Schedule
     *
     * @queryParam per_page int How many items to show per page. Example: 20
     * @queryParam page int Which page to show. Example: 2
     * @queryParam sort string Field to sort by. Enum: name, id, scheduled_at, completed_at, enabled Example: name
     * @queryParam include string Include related resources. Enum: components Example: components
     * @queryParam filters string[] Filter the resources. Example: name=api
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
     *
     * @apiResourceModel \Cachet\Models\Schedule
     *
     * @authenticated
     */
    public function store(CreateScheduleData $data, CreateSchedule $createScheduleAction)
    {
        $schedule = $createScheduleAction->handle($data);

        return ScheduleResource::make($schedule);
    }

    /**
     * Get Schedule
     *
     * @apiResource \Cachet\Http\Resources\Schedule
     *
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
     *
     * @apiResourceModel \Cachet\Models\Schedule
     *
     * @authenticated
     */
    public function update(UpdateScheduleData $data, Schedule $schedule, UpdateSchedule $updateScheduleAction)
    {
        $updateScheduleAction->handle($schedule, $data);

        return ScheduleResource::make($schedule->fresh());
    }

    /**
     * Delete Schedule
     *
     * @response 204
     *
     * @authenticated
     */
    public function destroy(Schedule $schedule, DeleteSchedule $deleteScheduleAction)
    {
        $deleteScheduleAction->handle($schedule);

        return response()->noContent();
    }
}
