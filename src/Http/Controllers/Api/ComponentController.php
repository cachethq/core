<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Actions\Component\CreateComponent;
use Cachet\Actions\Component\DeleteComponent;
use Cachet\Actions\Component\UpdateComponent;
use Cachet\Concerns\GuardsApiAbilities;
use Cachet\Data\Requests\Component\CreateComponentRequestData;
use Cachet\Data\Requests\Component\UpdateComponentRequestData;
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
    use GuardsApiAbilities;

    /**
     * The list of allowed includes.
     */
    public const ALLOWED_INCLUDES = [
        'group',
        'incidents',
    ];

    /**
     * List Components
     *
     * @apiResourceCollection \Cachet\Http\Resources\Component
     *
     * @apiResourceModel \Cachet\Models\Component
     *
     * @queryParam per_page int How many items to show per page. Example: 20
     * @queryParam page int Which page to show. Example: 2
     * @queryParam sort Field to sort by. Enum: name, status, enabled. Example: name
     * @queryParam include Include related resources. Enum: group, incidents. Example: group,incidents
     * @queryParam filters[name] string Filter by name. Example: My Component
     * @queryParam filters[status] Filter by status. Enum: 1 , 2, 3. Example: 1
     * @queryParam filters[enabled] Filter by enabled status. Enum: 0, 1. Example: 1
     */
    public function index()
    {
        $components = QueryBuilder::for(Component::class)
            ->allowedIncludes(self::ALLOWED_INCLUDES)
            ->allowedFilters(['name', 'status', 'enabled'])
            ->allowedSorts(['name', 'order', 'id'])
            ->simplePaginate(request('per_page', 15));

        return ComponentResource::collection($components);
    }

    /**
     * Create Component
     *
     * @apiResource \Cachet\Http\Resources\Component
     *
     * @apiResourceModel \Cachet\Models\Component
     *
     * @authenticated
     */
    public function store(CreateComponentRequestData $data, CreateComponent $createComponentAction)
    {
        $this->guard('components.manage');

        $component = $createComponentAction->handle(
            $data,
        );

        return ComponentResource::make($component);
    }

    /**
     * Get Component
     *
     * @apiResource \Cachet\Http\Resources\Component
     *
     * @apiResourceModel \Cachet\Models\Component
     *
     * @queryParam include Include related resources. Enum: group, incidents. Example: group,incidents
     */
    public function show(Component $component)
    {
        $componentQuery = QueryBuilder::for($component)
            ->allowedIncludes(self::ALLOWED_INCLUDES)
            ->first();

        return ComponentResource::make($componentQuery)
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update Component
     *
     * @apiResource \Cachet\Http\Resources\Component
     *
     * @apiResourceModel \Cachet\Models\Component
     *
     * @authenticated
     */
    public function update(UpdateComponentRequestData $data, Component $component, UpdateComponent $updateComponentAction)
    {
        $this->guard('components.manage');

        $updateComponentAction->handle($component, $data);

        return ComponentResource::make($component->fresh());
    }

    /**
     * Delete Component
     *
     * @response 204
     *
     * @authenticated
     */
    public function destroy(Component $component, DeleteComponent $deleteComponentAction)
    {
        $this->guard('components.delete');

        // @todo what happens to incidents linked to this component?
        // @todo re-calculate existing component orders?

        $deleteComponentAction->handle($component);

        return response()->noContent();
    }
}
