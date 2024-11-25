<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Actions\Update\CreateUpdate;
use Cachet\Actions\Update\DeleteUpdate;
use Cachet\Actions\Update\EditUpdate;
use Cachet\Http\Requests\CreateIncidentUpdateRequest;
use Cachet\Http\Requests\UpdateIncidentUpdateRequest;
use Cachet\Http\Resources\Update as IncidentUpdateResource;
use Cachet\Models\Incident;
use Cachet\Models\Update;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @group Incident Updates
 */
class IncidentUpdateController extends Controller
{
    /**
     * List Incident Updates
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
    public function index(Incident $incident)
    {
        $updates = QueryBuilder::for(Update::class)
            ->where('updateable_id', $incident->id)
            ->where('updateable_type', 'incident')
            ->allowedFilters(['status'])
            ->allowedSorts(['status', 'created_at'])
            ->simplePaginate(request('per_page', 15));

        return IncidentUpdateResource::collection($updates);
    }

    /**
     * Create Incident Update
     *
     * @apiResource \Cachet\Http\Resources\Update
     *
     * @apiResourceModel \Cachet\Models\Update
     *
     * @authenticated
     */
    public function store(CreateIncidentUpdateRequest $request, Incident $incident, CreateUpdate $createUpdateAction)
    {
        $update = $createUpdateAction->handle($incident, $request->validated());

        return IncidentUpdateResource::make($update);
    }

    /**
     * Get Incident Update
     *
     * @apiResource \Cachet\Http\Resources\Update
     *
     * @apiResourceModel \Cachet\Models\Update
     */
    public function show(Incident $incident, Update $update)
    {
        return IncidentUpdateResource::make($update)
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update Incident Update
     *
     * @apiResource \Cachet\Http\Resources\Update
     *
     * @apiResourceModel \Cachet\Models\Update
     *
     * @authenticated
     */
    public function update(UpdateIncidentUpdateRequest $request, Incident $incident, Update $update, EditUpdate $editUpdateAction)
    {
        $editUpdateAction->handle($update, $request->validated());

        return IncidentUpdateResource::make($update->fresh());
    }

    /**
     * Delete Incident Update
     *
     * @response 204
     *
     * @authenticated
     */
    public function destroy(Incident $incident, Update $update, DeleteUpdate $deleteIncidentUpdateAction)
    {
        $deleteIncidentUpdateAction->handle($update);

        return response()->noContent();
    }
}
