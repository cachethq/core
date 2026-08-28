<?php

namespace Cachet\Filament\Components;

use Cachet\Models\Component;
use Cachet\Models\ComponentGroup;
use Illuminate\Database\Eloquent\Model;

class ComponentOptions
{
    /**
     * Get components grouped by their component group, for Filament Select dropdowns.
     *
     * Pass the parent record to exclude components already attached to it through
     * the `components` BelongsToMany relationship.
     *
     * @return array<string, array<int, string>>
     */
    public static function forSelect(?Model $excludeAttachedTo = null): array
    {
        $query = Component::query()
            ->with('group')
            ->leftJoin('component_groups', 'components.component_group_id', '=', 'component_groups.id')
            ->orderByRaw('component_groups.id is null')
            ->orderBy('component_groups.order')
            ->orderBy('component_groups.name')
            ->orderBy('components.order')
            ->orderBy('components.name')
            ->select('components.*');

        if ($excludeAttachedTo && method_exists($excludeAttachedTo, 'components')) {
            $query->whereNotIn('components.id', $excludeAttachedTo->components()->pluck('components.id'));
        }

        return $query
            ->get()
            ->groupBy(function (Component $component): string {
                $group = $component->group;

                return $group instanceof ComponentGroup
                    ? $group->name
                    : __('cachet::component.list.ungrouped');
            })
            ->map(fn ($components): array => $components->pluck('name', 'id')->all())
            ->all();
    }
}
