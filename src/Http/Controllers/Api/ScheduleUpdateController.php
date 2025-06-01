<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Actions\Update\CreateUpdate;
use Cachet\Actions\Update\DeleteUpdate;
use Cachet\Actions\Update\EditUpdate;
use Cachet\Concerns\GuardsApiAbilities;
use Cachet\Data\Requests\ScheduleUpdate\CreateScheduleUpdateRequestData;
use Cachet\Data\Requests\ScheduleUpdate\EditScheduleUpdateRequestData;
use Cachet\Http\Resources\Update as UpdateResource;
use Cachet\Models\Schedule;
use Cachet\Models\Update;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Spatie\QueryBuilder\AllowedInclude;
use Spatie\QueryBuilder\QueryBuilder;

#[Group('Schedule Updates', weight: 9)]
class ScheduleUpdateController extends Controller
{
    use GuardsApiAbilities;

    /**
     * List Schedule Updates
     */
    #[QueryParameter('per_page', 'How many items to show per page.', type: 'int', default: 15, example: 20)]
    #[QueryParameter('page', 'Which page to show.', type: 'int', example: 2)]
    public function index(Schedule $schedule)
    {
        $query = Update::query()
            ->where('updateable_id', $schedule->id)
            ->where('updateable_type', Relation::getMorphAlias(Schedule::class));

        $updates = QueryBuilder::for($query)
            ->allowedSorts(['created_at'])
            ->allowedIncludes(['schedule'])
            ->simplePaginate(request('per_page', 15));

        return UpdateResource::collection($updates);
    }

    /**
     * Create Schedule Update
     */
    public function store(CreateScheduleUpdateRequestData $data, Schedule $schedule, CreateUpdate $createUpdateAction)
    {
        $this->guard('schedule-updates.manage');

        $update = $createUpdateAction->handle($schedule, $data);

        return UpdateResource::make($update);
    }

    /**
     * Get Schedule Update
     */
    public function show(Schedule $schedule, Update $update)
    {
        $updateQuery = QueryBuilder::for(Update::class)
            ->allowedIncludes([
                AllowedInclude::relationship('schedule', 'updateable'),
            ])
            ->find($update->id);

        return UpdateResource::make($updateQuery)
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update Schedule Update
     */
    public function update(EditScheduleUpdateRequestData $data, Schedule $schedule, Update $update, EditUpdate $editUpdateAction)
    {
        $this->guard('schedule-updates.manage');

        $editUpdateAction->handle($update, $data);

        return UpdateResource::make($update->fresh());
    }

    /**
     * Delete Schedule Update
     */
    public function destroy(Schedule $schedule, Update $update, DeleteUpdate $deleteUpdateAction)
    {
        $this->guard('schedule-updates.delete');

        $deleteUpdateAction->handle($update);

        return response()->noContent();
    }
}
