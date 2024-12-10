<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Actions\Update\CreateUpdate;
use Cachet\Actions\Update\DeleteUpdate;
use Cachet\Actions\Update\EditUpdate;
use Cachet\Data\ScheduleUpdate\CreateScheduleUpdateData;
use Cachet\Data\ScheduleUpdate\EditScheduleUpdateData;
use Cachet\Http\Resources\Update as UpdateResource;
use Cachet\Models\Incident;
use Cachet\Models\Schedule;
use Cachet\Models\Update;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
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
     * @queryParam sort string Field to sort by. Enum: name, created_at Example: name
     * @queryParam filters string[] Filter the resources.
     */
    public function index(Schedule $schedule)
    {
        $query = Update::query()
            ->where('updateable_id', $schedule->id)
            ->where('updateable_type', Relation::getMorphAlias(Schedule::class));

        $updates = QueryBuilder::for($query)
            ->allowedSorts(['created_at'])
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
    public function store(CreateScheduleUpdateData $data, Schedule $schedule, CreateUpdate $createUpdateAction)
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
     */
    public function show(Incident $incident, Schedule $schedule)
    {
        return UpdateResource::make($schedule)
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
    public function update(EditScheduleUpdateData $data, Schedule $schedule, Update $update, EditUpdate $editUpdateAction)
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
