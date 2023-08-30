<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Actions\Component\CreateComponent;
use Cachet\Actions\Component\DeleteComponent;
use Cachet\Actions\Component\UpdateComponent;
use Cachet\Data\ComponentData;
use Cachet\Http\Requests\CreateComponentRequest;
use Cachet\Http\Requests\UpdateComponentRequest;
use Cachet\Http\Resources\Component as ComponentResource;
use Cachet\Models\Component;
use Illuminate\Http\Request;
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
    public function store(CreateComponentRequest $request)
    {
        $component = CreateComponent::run($request->validated());

        return ComponentResource::make($component);
    }

    /**
     * Get Component.
     */
    public function show(Component $component)
    {
        $component = QueryBuilder::for($component)
            ->allowedIncludes(['group', 'incidents'])
            ->first();

        return ComponentResource::make($component)
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update Component.
     */
    public function update(UpdateComponentRequest $request, Component $component)
    {
        // @todo can we improve this?
        $request->mergeIfMissing($component->toArray());

        UpdateComponent::run($component, $request->validated());

        return ComponentResource::make($component->fresh());
    }

    /**
     * Delete Component.
     */
    public function destroy(Component $component)
    {
        // @todo what happens to incidents linked to this component?
        // @todo re-calculate existing component orders?

        DeleteComponent::run($component);

        return response()->noContent();
    }
}
