<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Actions\Update\CreateUpdate;
use Cachet\Actions\Update\DeleteUpdate;
use Cachet\Actions\Update\EditUpdate;
use Cachet\Data\Requests\ScheduleUpdate\CreateScheduleUpdateRequestData;
use Cachet\Data\Requests\ScheduleUpdate\EditScheduleUpdateRequestData;
use Cachet\Http\Resources\Update as UpdateResource;
use Cachet\Models\Schedule;
use Cachet\Models\Update;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Pagination\Paginator;
use Illuminate\Routing\Controller;
use Spatie\QueryBuilder\AllowedInclude;
use Spatie\QueryBuilder\QueryBuilder;

#[Group('Schedule Updates', weight: 9)]
class ScheduleUpdateController extends Controller
{
    /**
     * List Schedule Updates
     *
     * @response AnonymousResourceCollection<Paginator<UpdateResource>>
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
        $update = $createUpdateAction->handle($schedule, $data);

        return UpdateResource::make($update);
    }

    /**
     * Get Schedule Update
     */
    public function show(Schedule $schedule, Update $update)
    {
        $updateQuery = QueryBuilder::for($update)
            ->allowedIncludes([
                AllowedInclude::relationship('schedule', 'updateable'),
            ])
            ->first();

        return UpdateResource::make($updateQuery)
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update Schedule Update
     */
    public function update(EditScheduleUpdateRequestData $data, Schedule $schedule, Update $update, EditUpdate $editUpdateAction)
    {
        $editUpdateAction->handle($update, $data);

        return UpdateResource::make($update->fresh());
    }

    /**
     * Delete Schedule Update
     */
    public function destroy(Schedule $schedule, Update $update, DeleteUpdate $deleteUpdateAction)
    {
        $deleteUpdateAction->handle($update);

        return response()->noContent();
    }
}
