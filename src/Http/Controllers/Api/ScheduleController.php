<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Actions\Schedule\CreateSchedule;
use Cachet\Actions\Schedule\DeleteSchedule;
use Cachet\Actions\Schedule\UpdateSchedule;
use Cachet\Concerns\GuardsApiAbilities;
use Cachet\Data\Requests\Schedule\CreateScheduleRequestData;
use Cachet\Data\Requests\Schedule\UpdateScheduleRequestData;
use Cachet\Enums\ScheduleStatusEnum;
use Cachet\Filters\ScheduleStatusFilter;
use Cachet\Http\Resources\Schedule as ScheduleResource;
use Cachet\Models\Schedule;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

#[Group('Schedules', weight: 8)]
class ScheduleController extends Controller
{
    use GuardsApiAbilities;

    /**
     * List Schedules
     */
    #[QueryParameter('filter[name]', 'Filter the resources by name.', example: 'api')]
    #[QueryParameter('filter[status]', 'Filter the resources by status.', type: ScheduleStatusEnum::class)]
    #[QueryParameter('per_page', 'How many items to show per page.', type: 'int', default: 15, example: 20)]
    #[QueryParameter('page', 'Which page to show.', type: 'int', example: 2)]
    public function index()
    {
        $schedules = QueryBuilder::for(Schedule::class)
            ->allowedIncludes(['components', 'updates', 'user'])
            ->allowedFilters(['name', AllowedFilter::custom('status', new ScheduleStatusFilter)])
            ->allowedSorts(['name', 'id', 'scheduled_at', 'completed_at'])
            ->simplePaginate(request('per_page', 15));

        return ScheduleResource::collection($schedules);
    }

    /**
     * Create Schedule
     */
    public function store(CreateScheduleRequestData $data, CreateSchedule $createScheduleAction)
    {
        $this->guard('schedules.manage');

        $schedule = $createScheduleAction->handle($data);

        return ScheduleResource::make($schedule);
    }

    /**
     * Get Schedule
     */
    public function show(Schedule $schedule)
    {
        $scheduleQuery = QueryBuilder::for(Schedule::class)
            ->allowedIncludes(['components', 'updates', 'user'])
            ->find($schedule->id);

        return ScheduleResource::make($scheduleQuery)
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update Schedule
     */
    public function update(UpdateScheduleRequestData $data, Schedule $schedule, UpdateSchedule $updateScheduleAction)
    {
        $this->guard('schedules.manage');

        $updateScheduleAction->handle($schedule, $data);

        return ScheduleResource::make($schedule->fresh());
    }

    /**
     * Delete Schedule
     */
    public function destroy(Schedule $schedule, DeleteSchedule $deleteScheduleAction)
    {
        $this->guard('schedules.delete');

        $deleteScheduleAction->handle($schedule);

        return response()->noContent();
    }
}
