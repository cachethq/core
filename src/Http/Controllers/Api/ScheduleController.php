<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Actions\Schedule\CreateSchedule;
use Cachet\Actions\Schedule\DeleteSchedule;
use Cachet\Actions\Schedule\UpdateSchedule;
use Cachet\Data\Requests\Schedule\CreateScheduleRequestData;
use Cachet\Data\Requests\Schedule\UpdateScheduleRequestData;
use Cachet\Http\Resources\Schedule as ScheduleResource;
use Cachet\Models\Schedule;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Spatie\QueryBuilder\QueryBuilder;

#[Group('Schedules', weight: 8)]
class ScheduleController extends Controller
{
    /**
     * List Schedules
     *
     * @queryParam per_page int How many items to show per page. Example: 20
     * @queryParam page int Which page to show. Example: 2
     * @queryParam sort Field to sort by. Enum: name, id, scheduled_at, completed_at, enabled. Example: name
     * @queryParam include Include related resources. Enum: components, updates, user. Example: components
     * @queryParam filters[name] string Filter the resources. Example: name=api
     */
    public function index()
    {
        $schedules = QueryBuilder::for(Schedule::class)
            ->allowedIncludes(['components', 'updates', 'user'])
            ->allowedFilters(['name'])
            ->allowedSorts(['name', 'id', 'scheduled_at', 'completed_at'])
            ->simplePaginate(request('per_page', 15));

        return ScheduleResource::collection($schedules);
    }

    /**
     * Create Schedule
     */
    public function store(CreateScheduleRequestData $data, CreateSchedule $createScheduleAction)
    {
        $schedule = $createScheduleAction->handle($data);

        return ScheduleResource::make($schedule);
    }

    /**
     * Get Schedule
     *
     * @queryParam include Include related resources. Enum: components, updates, user. Example: components
     */
    public function show(Schedule $schedule)
    {
        $scheduleQuery = QueryBuilder::for($schedule)
            ->allowedIncludes(['components', 'updates', 'user'])
            ->first();

        return ScheduleResource::make($scheduleQuery)
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update Schedule
     */
    public function update(UpdateScheduleRequestData $data, Schedule $schedule, UpdateSchedule $updateScheduleAction)
    {
        $updateScheduleAction->handle($schedule, $data);

        return ScheduleResource::make($schedule->fresh());
    }

    /**
     * Delete Schedule
     */
    public function destroy(Schedule $schedule, DeleteSchedule $deleteScheduleAction)
    {
        $deleteScheduleAction->handle($schedule);

        return response()->noContent();
    }
}
