<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Actions\ComponentGroup\CreateComponentGroup;
use Cachet\Actions\ComponentGroup\DeleteComponentGroup;
use Cachet\Actions\ComponentGroup\UpdateComponentGroup;
use Cachet\Http\Requests\CreateComponentGroupRequest;
use Cachet\Http\Requests\UpdateComponentGroupRequest;
use Cachet\Http\Resources\ComponentGroup as ComponentGroupResource;
use Cachet\Models\ComponentGroup;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Spatie\QueryBuilder\QueryBuilder;

class ComponentGroupController extends Controller
{
    /**
     * Display a listing of the resource.
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
     * Store a newly created resource in storage.
     */
    public function store(CreateComponentGroupRequest $request)
    {
        $componentGroup = CreateComponentGroup::run($request->validated());

        return ComponentGroupResource::make($componentGroup);
    }

    /**
     * Display the specified resource.
     */
    public function show(ComponentGroup $componentGroup)
    {
        $componentGroup = QueryBuilder::for($componentGroup)
            ->allowedIncludes(['components'])
            ->first();

        return ComponentGroupResource::make($componentGroup)
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateComponentGroupRequest $request, ComponentGroup $componentGroup)
    {
        UpdateComponentGroup::run($componentGroup, $request->validated());

        return ComponentGroupResource::make($componentGroup->fresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ComponentGroup $componentGroup)
    {
        DeleteComponentGroup::run($componentGroup);

        return response()->noContent();
    }
}
