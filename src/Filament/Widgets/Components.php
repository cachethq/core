<?php

namespace Cachet\Filament\Widgets;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Models\Component;
use Cachet\Models\ComponentGroup;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class Components extends Widget implements HasSchemas
{
    use InteractsWithSchemas;

    protected string $view = 'cachet::filament.widgets.components';

    protected int|string|array $columnSpan = 'full';

    public Collection $formData;

    public Collection $components;

    public function mount(): void
    {
        $this->components = $components = Component::query()
            ->select(['id', 'component_group_id', 'name', 'status', 'enabled'])
            ->enabled()
            ->orderBy('order')
            ->get();

        $this->formData = $components->mapWithKeys(function (Component $component) {
            return [$component->id => ['status' => $component->status]];
        });
    }

    public function form(Schema $form): Schema
    {
        $componentGroupSchema = $this->loadVisibleComponentGroups()
            ->filter(fn (ComponentGroup $componentGroup) => $this->components->pluck('component_group_id')->contains($componentGroup->id))
            ->map(function (ComponentGroup $componentGroup): \Filament\Schemas\Components\Component {
                return Section::make($componentGroup->name)
                    ->schema(function () use ($componentGroup) {
                        return $this->components
                            ->filter(fn (Component $component) => $componentGroup->is($component->group))
                            ->map(fn (Component $component) => Group::make([$this->buildToggleButton($component)]))
                            ->toArray();
                    })
                    ->collapsed($componentGroup->isCollapsible())
                    ->persistCollapsed();
            })
            ->all();

        $ungroupedComponentSchema = $this->components
            ->filter(fn (Component $component) => is_null($component->component_group_id))
            ->map(function (Component $component): \Filament\Schemas\Components\Component {
                return Section::make($component->name)
                    ->schema(fn () => [$this->buildToggleButton($component)])
                    ->collapsible()
                    ->persistCollapsed();
            })
            ->all();

        return $form->components([
            ...$componentGroupSchema,
            ...$ungroupedComponentSchema,
        ])->statePath('formData');
    }

    protected function buildToggleButton(Component $component): ToggleButtons
    {
        return ToggleButtons::make($component->id.'.status')
            ->label($component->name)
            ->hiddenLabel(is_null($component->component_group_id))
            ->inline()
            ->live()
            ->options(ComponentStatusEnum::class)
            ->afterStateUpdated(fn (ComponentStatusEnum $state) => $component->update(['status' => $state]));
    }

    protected function loadVisibleComponentGroups(): Collection
    {
        return ComponentGroup::query()
            ->select(['id', 'name', 'collapsed', 'visible'])
            ->where('visible', '=', true)
            ->orderBy('order')
            ->get();
    }
}
