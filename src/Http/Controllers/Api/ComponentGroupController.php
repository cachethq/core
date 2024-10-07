<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Actions\ComponentGroup\CreateComponentGroup;
use Cachet\Actions\ComponentGroup\DeleteComponentGroup;
use Cachet\Actions\ComponentGroup\UpdateComponentGroup;
use Cachet\Data\ComponentGroup\CreateComponentGroupData;
use Cachet\Data\ComponentGroup\UpdateComponentGroupData;
use Cachet\Http\Resources\ComponentGroup as ComponentGroupResource;
use Cachet\Models\ComponentGroup;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Spatie\QueryBuilder\QueryBuilder;

class ComponentGroupController extends Controller
{
    /**
     * List Component Groups.
     */
    public function index()
    {
        $componentGroups = QueryBuilder::for(ComponentGroup::class)
            ->allowedIncludes(['components'])
            ->allowedSorts(['name', 'id'])
            ->simplePaginate(request('per_page', 15));

        return ComponentGroupResource::collection($componentGroups);
    }

    /**
     * Create Component Group.
     */
    public function store(CreateComponentGroupData $data, CreateComponentGroup $createComponentGroupAction)
    {
        $componentGroup = $createComponentGroupAction->handle($data);

        return ComponentGroupResource::make($componentGroup);
    }

    /**
     * Get Component Group.
     */
    public function show(ComponentGroup $componentGroup)
    {
        return ComponentGroupResource::make($componentGroup)
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update Component Group
     */
    public function update(UpdateComponentGroupData $data, ComponentGroup $componentGroup, UpdateComponentGroup $updateComponentGroupAction)
    {
        $updateComponentGroupAction->handle($componentGroup, $data);

        return ComponentGroupResource::make($componentGroup->fresh());
    }

    /**
     * Delete Component Group.
     */
    public function destroy(ComponentGroup $componentGroup, DeleteComponentGroup $deleteComponentGroupAction)
    {
        $deleteComponentGroupAction->handle($componentGroup);

        return response()->noContent();
    }
}
