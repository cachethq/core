<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Actions\Incident\CreateIncident;
use Cachet\Actions\Incident\DeleteIncident;
use Cachet\Actions\Incident\UpdateIncident;
use Cachet\Data\Requests\Incident\CreateIncidentRequestData;
use Cachet\Data\Requests\Incident\UpdateIncidentRequestData;
use Cachet\Http\Resources\Incident as IncidentResource;
use Cachet\Models\Incident;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Pagination\Paginator;
use Illuminate\Routing\Controller;
use Spatie\QueryBuilder\QueryBuilder;

#[Group('Incidents', weight: 3)]
class IncidentController extends Controller
{
    /**
     * The list of allowed includes.
     */
    public const ALLOWED_INCLUDES = [
        'components',
        'updates',
        'user',
    ];

    /**
     * List Incidents
     *
     * @return AnonymousResourceCollection<Paginator<IncidentResource>>
     */
    #[QueryParameter('per_page', 'How many items to show per page.', type: 'int', default: 15, example: 20)]
    #[QueryParameter('page', 'Which page to show.', type: 'int', example: 2)]
    public function index()
    {
        $query = Incident::query()
            ->when(! request('sort'), function (Builder $builder) {
                $builder->orderByDesc('created_at');
            });

        $incidents = QueryBuilder::for($query)
            ->allowedIncludes(self::ALLOWED_INCLUDES)
            ->allowedFilters(['name', 'status', 'occurred_at'])
            ->allowedSorts(['name', 'status', 'id'])
            ->simplePaginate(request('per_page', 15));

        return IncidentResource::collection($incidents);
    }

    /**
     * Create Incident
     */
    public function store(CreateIncidentRequestData $data, CreateIncident $createIncidentAction)
    {
        $incident = $createIncidentAction->handle($data);

        return IncidentResource::make($incident);
    }

    /**
     * Get Incident
     */
    public function show(Incident $incident)
    {
        $incidentQuery = QueryBuilder::for($incident)
            ->allowedIncludes(self::ALLOWED_INCLUDES)
            ->first();

        return IncidentResource::make($incidentQuery)
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update Incident
     */
    public function update(UpdateIncidentRequestData $data, Incident $incident, UpdateIncident $updateIncidentAction)
    {
        $updateIncidentAction->handle($incident, $data);

        return IncidentResource::make($incident->fresh());
    }

    /**
     * Delete Incident
     */
    public function destroy(Incident $incident, DeleteIncident $deleteIncidentAction)
    {
        $deleteIncidentAction->handle($incident);

        return response()->noContent();
    }
}
