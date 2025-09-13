<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Actions\ComponentGroup\CreateComponentGroup;
use Cachet\Actions\ComponentGroup\DeleteComponentGroup;
use Cachet\Actions\ComponentGroup\UpdateComponentGroup;
use Cachet\Concerns\GuardsApiAbilities;
use Cachet\Data\Requests\ComponentGroup\CreateComponentGroupRequestData;
use Cachet\Data\Requests\ComponentGroup\UpdateComponentGroupRequestData;
use Cachet\Http\Resources\ComponentGroup as ComponentGroupResource;
use Cachet\Models\ComponentGroup;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Spatie\QueryBuilder\QueryBuilder;

#[Group('Component Groups', weight: 2)]
class ComponentGroupController extends Controller
{
    use GuardsApiAbilities;

    /**
     * List Component Groups
     */
    #[QueryParameter('per_page', 'How many items to show per page.', type: 'int', default: 15, example: 20)]
    #[QueryParameter('page', 'Which page to show.', type: 'int', example: 2)]
    public function index()
    {
        $componentGroups = QueryBuilder::for(ComponentGroup::class)
            ->allowedIncludes(['components'])
            ->allowedSorts(['name', 'id'])
            ->simplePaginate(request('per_page', 15));

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
    public function show(ComponentGroup $componentGroup)
    {

        $componentQuery = QueryBuilder::for(ComponentGroup::class)
            ->allowedIncludes(['components'])
            ->find($componentGroup->id);

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
