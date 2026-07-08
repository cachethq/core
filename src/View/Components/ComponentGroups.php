<?php

namespace Cachet\View\Components;

use Cachet\Models\Component;
use Cachet\Models\ComponentGroup;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\View\Component as ViewComponent;
use Illuminate\View\View;

class ComponentGroups extends ViewComponent
{
    public function render(): View|Closure|string
    {
        return view('cachet::components.component-groups', [
            'componentGroups' => $this->componentGroups(),
            'ungroupedComponents' => Component::query()
                ->enabled()
                ->whereNull('component_group_id')
                ->orderBy('order')
                ->with('unresolvedIncidents')
                ->withCount(['incidents' => fn ($query) => $query->unresolved()])
                ->get(),
        ]);
    }

    /**
     * Fetch component groups with their components in the configured order.
     */
    private function componentGroups(): Collection
    {
        return ComponentGroup::query()
            ->with([
                'components' => fn ($query) => $query->enabled()->orderBy('order')->withCount(['incidents' => fn ($query) => $query->unresolved()]),
                'components.unresolvedIncidents',
            ])
            ->visible(auth()->check())
            ->orderBy('order')
            ->when(auth()->check(), fn (Builder $query) => $query->users(), fn ($query) => $query->guests())
            ->get()
            ->map(function (ComponentGroup $group) {
                $group->setRelation('components', $group->components->sortBy(
                    fn (Component $component) => $component->orderableBy($group),
                    descending: $group->order_direction?->descending() ?? false,
                )->values());

                return $group;
            });
    }
}
