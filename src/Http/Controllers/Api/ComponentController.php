<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Actions\Component\CreateComponent;
use Cachet\Actions\Component\DeleteComponent;
use Cachet\Actions\Component\UpdateComponent;
use Cachet\Concerns\GuardsApiAbilities;
use Cachet\Data\Requests\Component\CreateComponentRequestData;
use Cachet\Data\Requests\Component\UpdateComponentRequestData;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Http\Resources\Component as ComponentResource;
use Cachet\Models\Component;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

#[Group('Components', weight: 1)]
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
     */
    #[QueryParameter('filter[status]', 'Filter by status', type: ComponentStatusEnum::class, example: 1)]
    #[QueryParameter('filter[name]', 'Filter by name.', example: 'My Component')]
    #[QueryParameter('filter[enabled]', 'Filter by enabled status.', type: 'bool', example: '1')]
    #[QueryParameter('per_page', 'How many items to show per page.', type: 'int', default: 15, example: 20)]
    #[QueryParameter('page', 'Which page to show.', type: 'int', example: 2)]
    public function index()
    {
        $components = QueryBuilder::for(Component::class)
            ->allowedIncludes(self::ALLOWED_INCLUDES)
            ->allowedFilters([
                'name',
                AllowedFilter::exact('status'),
                AllowedFilter::exact('enabled'),
            ])
            ->allowedSorts(['name', 'order', 'id'])
            ->simplePaginate(request('per_page', 15));

        return ComponentResource::collection($components);
    }

    /**
     * Create Component
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
     */
    public function show(Component $component)
    {

        $componentQuery = QueryBuilder::for(Component::class)
            ->allowedIncludes(self::ALLOWED_INCLUDES)
            ->find($component->id);

        return ComponentResource::make($componentQuery)
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update Component
     */
    public function update(UpdateComponentRequestData $data, Component $component, UpdateComponent $updateComponentAction)
    {
        $this->guard('components.manage');

        $updateComponentAction->handle($component, $data);

        return ComponentResource::make($component->fresh());
    }

    /**
     * Delete Component
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
