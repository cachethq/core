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
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Spatie\QueryBuilder\AllowedInclude;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @group Schedule Updates
 */
class ScheduleUpdateController extends Controller
{
    /**
     * List Schedule Updates
     *
     * @apiResourceCollection \Cachet\Http\Resources\Update
     *
     * @apiResourceModel \Cachet\Models\Update
     *
     * @queryParam per_page int How many items to show per page. Example: 20
     * @queryParam page int Which page to show. Example: 2
     * @queryParam sort Field to sort by. Enum: name, created_at. Example: name
     * @queryParam include Include related resources. Enum: schedule. Example: schedule
     */
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
     *
     * @apiResource \Cachet\Http\Resources\Update
     *
     * @apiResourceModel \Cachet\Models\Update
     *
     * @authenticated
     */
    public function store(CreateScheduleUpdateRequestData $data, Schedule $schedule, CreateUpdate $createUpdateAction)
    {
        $update = $createUpdateAction->handle($schedule, $data);

        return UpdateResource::make($update);
    }

    /**
     * Get Schedule Update
     *
     * @apiResource \Cachet\Http\Resources\Update
     *
     * @apiResourceModel \Cachet\Models\Update
     *
     * @queryParam include Include related resources. Enum: schedule. Example: schedule
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
     *
     * @apiResource \Cachet\Http\Resources\Update
     *
     * @apiResourceModel \Cachet\Models\Update
     *
     * @authenticated
     */
    public function update(EditScheduleUpdateRequestData $data, Schedule $schedule, Update $update, EditUpdate $editUpdateAction)
    {
        $editUpdateAction->handle($update, $data);

        return UpdateResource::make($update->fresh());
    }

    /**
     * Delete Schedule Update
     *
     * @response 204
     *
     * @authenticated
     */
    public function destroy(Schedule $schedule, Update $update, DeleteUpdate $deleteUpdateAction)
    {
        $deleteUpdateAction->handle($update);

        return response()->noContent();
    }
}
