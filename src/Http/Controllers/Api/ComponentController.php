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

/**
 * @group Components
 */
class ComponentController extends Controller
{
    /**
     * List Components
     *
     * @apiResourceCollection \Cachet\Http\Resources\Component
     * @apiResourceModel \Cachet\Models\Component
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
     * Create Component
     *
     * @apiResource \Cachet\Http\Resources\Component
     * @apiResourceModel \Cachet\Models\Component
     * @authenticated
     */
    public function store(CreateComponentRequest $request, CreateComponent $createComponentAction)
    {
        $component = $createComponentAction->handle($request->validated());

        return ComponentResource::make($component);
    }

    /**
     * Get Component
     *
     * @apiResource \Cachet\Http\Resources\Component
     * @apiResourceModel \Cachet\Models\Component
     */
    public function show(Component $component)
    {
        return ComponentResource::make($component)
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update Component
     *
     * @apiResource \Cachet\Http\Resources\Component
     * @apiResourceModel \Cachet\Models\Component
     * @authenticated
     */
    public function update(UpdateComponentRequest $request, Component $component, UpdateComponent $updateComponentAction)
    {
        $updateComponentAction->handle($component, $request->validated());

        return ComponentResource::make($component->fresh());
    }

    /**
     * Delete Component
     *
     * @response 204
     * @authenticated
     */
    public function destroy(Component $component, DeleteComponent $deleteComponentAction)
    {
        // @todo what happens to incidents linked to this component?
        // @todo re-calculate existing component orders?

        $deleteComponentAction->handle($component);

        return response()->noContent();
    }
}
