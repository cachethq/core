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
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use Spatie\QueryBuilder\QueryBuilder;

#[Group('Incident Updates', weight: 4)]
class IncidentUpdateController extends Controller
{
    use GuardsApiAbilities;

    /**
     * List Incident Updates
     */
    #[QueryParameter('per_page', 'How many items to show per page.', type: 'int', default: 15, example: 20)]
    #[QueryParameter('page', 'Which page to show.', type: 'int', example: 2)]
    public function index(Incident $incident)
    {
        $query = Update::query()
            ->where('updateable_id', $incident->id)
            ->where('updateable_type', 'incident');

        $updates = QueryBuilder::for($query)
            ->allowedFilters([AllowedFilter::exact('status')])
            ->allowedIncludes(['incident'])
            ->allowedSorts(['status', 'created_at'])
            ->simplePaginate(request('per_page', 15));

        return UpdateResource::collection($updates);
    }

    /**
     * Create Incident Update
     */
    public function store(CreateIncidentUpdateRequestData $data, Incident $incident, CreateUpdate $createUpdateAction)
    {
        $this->guard('incident-updates.manage');

        $update = $createUpdateAction->handle($incident, $data);

        return UpdateResource::make($update);
    }

    /**
     * Get Incident Update
     */
    public function show(Incident $incident, Update $update)
    {
        $updateQuery = QueryBuilder::for(Update::class)
            ->allowedIncludes([
                AllowedInclude::relationship('incident', 'updateable'),
            ])
            ->find($update->id);

        return UpdateResource::make($updateQuery)
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update Incident Update
     */
    public function update(EditIncidentUpdateRequestData $data, Incident $incident, Update $update, EditUpdate $editUpdateAction)
    {
        $this->guard('incident-updates.manage');

        $editUpdateAction->handle($update, $data);

        return UpdateResource::make($update->fresh());
    }

    /**
     * Delete Incident Update
     */
    public function destroy(Incident $incident, Update $update, DeleteUpdate $deleteUpdateAction)
    {
        $this->guard('incident-updates.delete');

        $deleteUpdateAction->handle($update);

        return response()->noContent();
    }
}
