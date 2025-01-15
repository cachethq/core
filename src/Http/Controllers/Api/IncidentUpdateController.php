<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Actions\Update\CreateUpdate;
use Cachet\Actions\Update\DeleteUpdate;
use Cachet\Actions\Update\EditUpdate;
use Cachet\Concerns\GuardsApiAbilities;
use Cachet\Data\Requests\IncidentUpdate\CreateIncidentUpdateRequestData;
use Cachet\Data\Requests\IncidentUpdate\EditIncidentUpdateRequestData;
use Cachet\Http\Resources\Update as UpdateResource;
use Cachet\Models\Incident;
use Cachet\Models\Update;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Spatie\QueryBuilder\AllowedInclude;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @group Incident Updates
 */
class IncidentUpdateController extends Controller
{
    use GuardsApiAbilities;

    /**
     * List Incident Updates
     *
     * @apiResourceCollection \Cachet\Http\Resources\Update
     *
     * @apiResourceModel \Cachet\Models\Update
     *
     * @queryParam per_page int How many items to show per page. Example: 20
     * @queryParam page int Which page to show. Example: 2
     * @queryParam sort Field to sort by. Enum: name, created_at. Example: name
     * @queryParam include Include related resources. Enum: incident. Example: incident
     */
    public function index(Incident $incident)
    {
        $query = Update::query()
            ->where('updateable_id', $incident->id)
            ->where('updateable_type', 'incident');

        $updates = QueryBuilder::for($query)
            ->allowedFilters(['status'])
            ->allowedIncludes(['incident'])
            ->allowedSorts(['status', 'created_at'])
            ->simplePaginate(request('per_page', 15));

        return UpdateResource::collection($updates);
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
    public function store(CreateIncidentUpdateRequestData $data, Incident $incident, CreateUpdate $createUpdateAction)
    {
        $this->guard('incident-updates.manage');

        $update = $createUpdateAction->handle($incident, $data);

        return UpdateResource::make($update);
    }

    /**
     * Get Incident Update
     *
     * @apiResource \Cachet\Http\Resources\Update
     *
     * @apiResourceModel \Cachet\Models\Update
     *
     * @queryParam include Include related resources. Enum: incident. Example: incident
     */
    public function show(Incident $incident, Update $update)
    {
        $updateQuery = QueryBuilder::for($update)
            ->allowedIncludes([
                AllowedInclude::relationship('incident', 'updateable'),
            ])
            ->first();

        return UpdateResource::make($updateQuery)
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
    public function update(EditIncidentUpdateRequestData $data, Incident $incident, Update $update, EditUpdate $editUpdateAction)
    {
        $this->guard('incident-updates.manage');

        $editUpdateAction->handle($update, $data);

        return UpdateResource::make($update->fresh());
    }

    /**
     * Delete Incident Update
     *
     * @response 204
     *
     * @authenticated
     */
    public function destroy(Incident $incident, Update $update, DeleteUpdate $deleteUpdateAction)
    {
        $this->guard('incident-updates.delete');

        $deleteUpdateAction->handle($update);

        return response()->noContent();
    }
}
