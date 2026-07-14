<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Actions\Incident\CreateIncident;
use Cachet\Actions\Incident\DeleteIncident;
use Cachet\Actions\Incident\UpdateIncident;
use Cachet\Concerns\ChecksApiAuthentication;
use Cachet\Concerns\GuardsApiAbilities;
use Cachet\Data\Requests\Incident\CreateIncidentRequestData;
use Cachet\Data\Requests\Incident\UpdateIncidentRequestData;
use Cachet\Filters\MetaFilter;
use Cachet\Http\Resources\Incident as IncidentResource;
use Cachet\Models\Component;
use Cachet\Models\ComponentGroup;
use Cachet\Models\Incident;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Number;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use Spatie\QueryBuilder\QueryBuilder;

#[Group('Incidents', weight: 3)]
class IncidentController extends Controller
{
    use ChecksApiAuthentication;
    use GuardsApiAbilities;

    /**
     * The list of allowed includes, scoped to the current caller.
     *
     * Component groups hidden from the caller are excluded from the nested
     * include so a visible incident cannot reveal them.
     *
     * @return array<int, string|Collection<int, AllowedInclude>>
     */
    protected function allowedIncludes(): array
    {
        return [
            'components',
            AllowedInclude::callback('components.group', function (BelongsTo $query): void {
                /** @var BelongsTo<ComponentGroup, Component> $query */
                $query->visible($this->isAuthenticated());
            }),
            'updates',
            'user',
            'meta',
        ];
    }

    /**
     * List Incidents
     */
    #[QueryParameter('filter[meta][key]', 'Filter by a metadata key/value pair.', example: 'eu-west')]
    #[QueryParameter('include', 'Include related data (components, components.group, updates, user, meta).', example: 'meta')]
    #[QueryParameter('per_page', 'How many items to show per page.', type: 'int', default: 15, example: 20)]
    #[QueryParameter('page', 'Which page to show.', type: 'int', example: 2)]
    public function index(Request $request)
    {
        $incidents = QueryBuilder::for(Incident::query()->with('updates')->visible($this->isAuthenticated()))
            ->allowedIncludes($this->allowedIncludes())
            ->allowedFilters([
                'name',
                AllowedFilter::exact('status'),
                AllowedFilter::scope('occurs_after'),
                AllowedFilter::scope('occurs_before'),
                AllowedFilter::scope('occurs_on'),
                AllowedFilter::custom('meta', new MetaFilter),
            ])
            ->allowedSorts(['name', 'status', 'id', 'created_at'])
            ->defaultSort('-created_at')
            ->simplePaginate(Number::clamp($request->integer('per_page', 15), min: 1, max: 100));

        return IncidentResource::collection($incidents);
    }

    /**
     * Create Incident
     */
    public function store(CreateIncidentRequestData $data, CreateIncident $createIncidentAction)
    {
        $this->guard('incidents.manage');

        $incident = $createIncidentAction->handle($data);

        return IncidentResource::make($incident);
    }

    /**
     * Get Incident
     */
    #[QueryParameter('include', 'Include related data (components, components.group, updates, user, meta).', example: 'meta')]
    public function show(Incident $incident)
    {
        $incidentQuery = QueryBuilder::for(Incident::query()->visible($this->isAuthenticated()))
            ->allowedIncludes($this->allowedIncludes())
            ->findOrFail($incident->id);

        return IncidentResource::make($incidentQuery)
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update Incident
     */
    public function update(UpdateIncidentRequestData $data, Incident $incident, UpdateIncident $updateIncidentAction)
    {
        $this->guard('incidents.manage');

        $updateIncidentAction->handle($incident, $data);

        return IncidentResource::make($incident->fresh());
    }

    /**
     * Delete Incident
     */
    public function destroy(Incident $incident, DeleteIncident $deleteIncidentAction)
    {
        $this->guard('incidents.delete');

        $deleteIncidentAction->handle($incident);

        return response()->noContent();
    }
}
