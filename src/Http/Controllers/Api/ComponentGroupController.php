<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Actions\ComponentGroup\CreateComponentGroup;
use Cachet\Actions\ComponentGroup\DeleteComponentGroup;
use Cachet\Actions\ComponentGroup\UpdateComponentGroup;
use Cachet\Concerns\ChecksApiAuthentication;
use Cachet\Concerns\GuardsApiAbilities;
use Cachet\Data\Requests\ComponentGroup\CreateComponentGroupRequestData;
use Cachet\Data\Requests\ComponentGroup\UpdateComponentGroupRequestData;
use Cachet\Filters\MetaFilter;
use Cachet\Http\Resources\ComponentGroup as ComponentGroupResource;
use Cachet\Models\Component;
use Cachet\Models\ComponentGroup;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Number;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use Spatie\QueryBuilder\QueryBuilder;

#[Group('Component Groups', weight: 2)]
class ComponentGroupController extends Controller
{
    use ChecksApiAuthentication;
    use GuardsApiAbilities;

    /**
     * The list of allowed includes, scoped to the current caller.
     *
     * Disabled components are hidden from guests, matching the status page.
     *
     * @return array<int, string|Collection<int, AllowedInclude>>
     */
    protected function allowedIncludes(): array
    {
        return [
            AllowedInclude::callback('components', function (HasMany $query): void {
                /** @var HasMany<Component, ComponentGroup> $query */
                if (! $this->isAuthenticated()) {
                    $query->enabled();
                }
            }),
            'meta',
        ];
    }

    /**
     * List Component Groups
     */
    #[QueryParameter('filter[meta][key]', 'Filter by a metadata key/value pair.', example: 'eu-west')]
    #[QueryParameter('include', 'Include related data (components, meta).', example: 'meta')]
    #[QueryParameter('per_page', 'How many items to show per page.', type: 'int', default: 15, example: 20)]
    #[QueryParameter('page', 'Which page to show.', type: 'int', example: 2)]
    public function index(Request $request)
    {
        $componentGroups = QueryBuilder::for(ComponentGroup::query()->visible($this->isAuthenticated()))
            ->allowedIncludes($this->allowedIncludes())
            ->allowedFilters([
                AllowedFilter::custom('meta', new MetaFilter),
            ])
            ->allowedSorts(['name', 'id'])
            ->simplePaginate(Number::clamp($request->integer('per_page', 15), min: 1, max: 100));

        return ComponentGroupResource::collection($componentGroups);
    }

    /**
     * Create Component Group
     */
    public function store(CreateComponentGroupRequestData $data, CreateComponentGroup $createComponentGroupAction)
    {
        $this->guard('component-groups.manage');

        $componentGroup = $createComponentGroupAction->handle($data);

        return ComponentGroupResource::make($componentGroup);
    }

    /**
     * Get Component Group
     */
    #[QueryParameter('include', 'Include related data (components, meta).', example: 'meta')]
    public function show(ComponentGroup $componentGroup)
    {
        $componentQuery = QueryBuilder::for(ComponentGroup::query()->visible($this->isAuthenticated()))
            ->allowedIncludes($this->allowedIncludes())
            ->findOrFail($componentGroup->id);

        return ComponentGroupResource::make($componentQuery)
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update Component Group
     */
    public function update(UpdateComponentGroupRequestData $data, ComponentGroup $componentGroup, UpdateComponentGroup $updateComponentGroupAction)
    {
        $this->guard('component-groups.manage');

        $updateComponentGroupAction->handle($componentGroup, $data);

        return ComponentGroupResource::make($componentGroup->fresh());
    }

    /**
     * Delete Component Group
     */
    public function destroy(ComponentGroup $componentGroup, DeleteComponentGroup $deleteComponentGroupAction)
    {
        $this->guard('component-groups.delete');
        $deleteComponentGroupAction->handle($componentGroup);

        return response()->noContent();
    }
}
