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

/**
 * @group Component Groups
 */
class ComponentGroupController extends Controller
{
    /**
     * List Component Groups
     *
     * @apiResource \Cachet\Http\Resources\ComponentGroup
     *
     * @apiResourceModel \Cachet\Models\ComponentGroup
     *
     * @queryParam per_page int How many items to show per page. Example: 20
     * @queryParam page int Which page to show. Example: 2
     * @queryParam sort Field to sort by. Enum: name, id. Example: name
     * @queryParam include Include related resources. Enum: components. Example: components
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
     * Create Component Group
     *
     * @apiResource \Cachet\Http\Resources\ComponentGroup
     *
     * @apiResourceModel \Cachet\Models\ComponentGroup
     *
     * @authenticated
     */
    public function store(CreateComponentGroupData $data, CreateComponentGroup $createComponentGroupAction)
    {
        $componentGroup = $createComponentGroupAction->handle($data);

        return ComponentGroupResource::make($componentGroup);
    }

    /**
     * Get Component Group
     *
     * @apiResource \Cachet\Http\Resources\ComponentGroup
     *
     * @apiResourceModel \Cachet\Models\ComponentGroup
     *
     * @queryParam include Include related resources. Enum: components. Example: components
     */
    public function show(ComponentGroup $componentGroup)
    {
        $componentQuery = QueryBuilder::for($componentGroup)
            ->allowedIncludes(['components'])
            ->first();

        return ComponentGroupResource::make($componentQuery)
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update Component Group
     *
     * @apiResource \Cachet\Http\Resources\ComponentGroup
     *
     * @apiResourceModel \Cachet\Models\ComponentGroup
     *
     * @authenticated
     */
    public function update(UpdateComponentGroupData $data, ComponentGroup $componentGroup, UpdateComponentGroup $updateComponentGroupAction)
    {
        $updateComponentGroupAction->handle($componentGroup, $data);

        return ComponentGroupResource::make($componentGroup->fresh());
    }

    /**
     * Delete Component Group
     *
     * @apiResource \Cachet\Http\Resources\ComponentGroup
     *
     * @apiResourceModel \Cachet\Models\ComponentGroup
     *
     * @authenticated
     */
    public function destroy(ComponentGroup $componentGroup, DeleteComponentGroup $deleteComponentGroupAction)
    {
        $deleteComponentGroupAction->handle($componentGroup);

        return response()->noContent();
    }
}
