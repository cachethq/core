<?php

namespace Cachet\Filament\Widgets;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Models\Component;
use Cachet\Models\ComponentGroup;
use Filament\Forms\Components\Component as FilamentFormComponent;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class Components extends Widget implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'cachet::filament.widgets.components';

    protected int|string|array $columnSpan = 'full';

    public Collection $formData;

    public Collection $components;

    public function mount(): void
    {
        $this->components = $components = Component::query()
            ->select(['id', 'component_group_id', 'name', 'status', 'enabled'])
            ->enabled()
            ->get();

        $this->formData = $components->mapWithKeys(function (Component $component) {
            return [$component->id => ['status' => $component->status]];
        });
    }

    public function form(Form $form): Form
    {
        $componentGroupSchema = $this->loadVisibleComponentGroups()
            ->filter(fn (ComponentGroup $componentGroup) => $this->components->pluck('component_group_id')->contains($componentGroup->id))
            ->map(function (ComponentGroup $componentGroup): FilamentFormComponent {
                return Section::make($componentGroup->name)
                    ->schema(function () use ($componentGroup) {
                        return $this->components
                            ->filter(fn (Component $component) => $componentGroup->is($component->group))
                            ->map(fn (Component $component) => Group::make([$this->buildToggleButton($component)]))
                            ->toArray();
                    })
                    ->collapsed($componentGroup->isCollapsible());
            });

        $ungroupedComponentSchema = $this->components->filter(fn(Component $component) => is_null($component->component_group_id))
            ->map(function (Component $component): FilamentFormComponent {
                return Section::make($component->name)
                    ->schema(fn () => [$this->buildToggleButton($component)])
                    ->collapsible(false);
            });

        $schema = $componentGroupSchema->merge($ungroupedComponentSchema)->toArray();

        return $form->schema($schema)->statePath('formData');
    }

    protected function buildToggleButton(Component $component): ToggleButtons
    {
        return ToggleButtons::make($component->id . '.status')
            ->label($component->name)
            ->inline()
            ->live()
            ->options(ComponentStatusEnum::class)
            ->afterStateUpdated(function (ComponentStatusEnum $state) use ($component) {
                return $component->update(['status' => $state]);
            });
    }

    protected function loadVisibleComponentGroups(): Collection
    {
        return ComponentGroup::query()
            ->select(['id', 'name', 'collapsed', 'visible'])
            ->where('visible', '=', true)
            ->get();
    }
}
