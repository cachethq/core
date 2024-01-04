<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Actions\Component\CreateComponent;
use Cachet\Actions\Component\DeleteComponent;
use Cachet\Actions\Component\UpdateComponent;
use Cachet\Http\Requests\CreateComponentRequest;
use Cachet\Http\Requests\UpdateComponentRequest;
use Cachet\Http\Resources\Component as ComponentResource;
use Cachet\Models\Component;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Spatie\QueryBuilder\QueryBuilder;

class ComponentController extends Controller
{
    /**
     * List Components.
     */
    public function index()
    {
        $components = QueryBuilder::for(Component::class)
            ->allowedIncludes(['group', 'incidents'])
            ->allowedFilters(['name', 'status', 'enabled'])
            ->allowedSorts(['name', 'order', 'id'])
            ->simplePaginate(request('per_page', 15));

        return ComponentResource::collection($components);
    }

    /**
     * Create Component.
     */
    public function store(CreateComponentRequest $request, CreateComponent $createComponentAction)
    {
        $component = $createComponentAction->handle($request->validated());

        return ComponentResource::make($component);
    }

    /**
     * Get Component.
     */
    public function show(Component $component)
    {
        return ComponentResource::make($component)
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update Component.
     */
    public function update(UpdateComponentRequest $request, Component $component, UpdateComponent $updateComponentAction)
    {
        $updateComponentAction->handle($component, $request->validated());

        return ComponentResource::make($component->fresh());
    }

    /**
     * Delete Component.
     */
    public function destroy(Component $component, DeleteComponent $deleteComponentAction)
    {
        // @todo what happens to incidents linked to this component?
        // @todo re-calculate existing component orders?

        $deleteComponentAction->handle($component);

        return response()->noContent();
    }
}
