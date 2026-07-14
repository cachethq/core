<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Actions\Component\CreateComponent;
use Cachet\Actions\Component\DeleteComponent;
use Cachet\Actions\Component\UpdateComponent;
use Cachet\Concerns\ChecksApiAuthentication;
use Cachet\Concerns\GuardsApiAbilities;
use Cachet\Data\Requests\Component\CreateComponentRequestData;
use Cachet\Data\Requests\Component\UpdateComponentRequestData;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Filters\MetaFilter;
use Cachet\Http\Resources\Component as ComponentResource;
use Cachet\Models\Component;
use Cachet\Models\ComponentGroup;
use Cachet\Models\Incident;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Number;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use Spatie\QueryBuilder\QueryBuilder;

#[Group('Components', weight: 1)]
class ComponentController extends Controller
{
    use ChecksApiAuthentication;
    use GuardsApiAbilities;

    /**
     * List Components
     */
    #[QueryParameter('filter[status]', 'Filter by status', type: ComponentStatusEnum::class, example: 1)]
    #[QueryParameter('filter[name]', 'Filter by name.', example: 'My Component')]
    #[QueryParameter('filter[enabled]', 'Filter by enabled status.', type: 'bool', example: '1')]
    #[QueryParameter('filter[meta][key]', 'Filter by a metadata key/value pair.', example: 'eu-west')]
    #[QueryParameter('include', 'Include related data (group, incidents, meta).', example: 'meta')]
    #[QueryParameter('per_page', 'How many items to show per page.', type: 'int', default: 15, example: 20)]
    #[QueryParameter('page', 'Which page to show.', type: 'int', example: 2)]
    public function index(Request $request)
    {
        $components = QueryBuilder::for($this->visibleComponents())
            ->allowedIncludes($this->allowedIncludes())
            ->allowedFilters([
                'name',
                AllowedFilter::exact('status'),
                AllowedFilter::exact('enabled')->default(true),
                AllowedFilter::custom('meta', new MetaFilter),
            ])
            ->allowedSorts(['name', 'order', 'id'])
            ->simplePaginate(Number::clamp($request->integer('per_page', 15), min: 1, max: 100));

        return ComponentResource::collection($components);
    }

    /**
     * The list of allowed includes, scoped to the current caller.
     *
     * @return array<int, string|Collection<int, AllowedInclude>>
     */
    protected function allowedIncludes(): array
    {
        return [
            'group',
            AllowedInclude::callback('incidents', function (BelongsToMany $query): void {
                /** @var BelongsToMany<Incident, Component> $query */
                $query->visible($this->isAuthenticated());
            }),
            'meta',
        ];
    }

    /**
     * Base query scoping components to those visible to the current caller.
     *
     * Components have no visibility of their own; they inherit it from their
     * group. Ungrouped components are always public and disabled components
     * are hidden from guests, matching the status page.
     *
     * @return Builder<Component>
     */
    protected function visibleComponents(): Builder
    {
        $visibleGroups = ComponentGroup::query()->visible($this->isAuthenticated())->select('id');

        return Component::query()
            ->unless($this->isAuthenticated(), fn (Builder $query) => $query->enabled())
            ->where(function ($query) use ($visibleGroups): void {
                $query->whereNull('component_group_id')
                    ->orWhereIn('component_group_id', $visibleGroups);
            });
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
    #[QueryParameter('include', 'Include related data (group, incidents, meta).', example: 'meta')]
    public function show(Component $component)
    {
        $componentQuery = QueryBuilder::for($this->visibleComponents())
            ->allowedIncludes($this->allowedIncludes())
            ->findOrFail($component->id);

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
