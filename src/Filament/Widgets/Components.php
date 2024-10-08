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

    public  Collection $components;

    public function mount(): void
    {
        $this->components = $components = Component::query()
            ->select(['id', 'component_group_id', 'name', 'status', 'enabled'])
            ->where('enabled', '=', true)
            ->get();

        $componentGroups = $this->loadVisibleComponentGroups();

        $this->formData = $componentGroups
            ->mapWithKeys(function (ComponentGroup $componentGroup) use ($components) {
                $components = $components->where('component_group_id', '=', $componentGroup->id);

                return [$componentGroup->id => ['components' => $components->mapWithKeys(function (Component $component) {
                    return [$component->id => $component->only('status')];
                })]];
            });
    }

    public function form(Form $form): Form
    {
        $componentGroups = $this->loadVisibleComponentGroups();

        $schema = $componentGroups
            ->filter(fn (ComponentGroup $componentGroup) => $this->components->pluck('component_group_id')->contains($componentGroup->id))
            ->map(function (ComponentGroup $componentGroup): FilamentFormComponent {
                return Section::make($componentGroup->name)
                    ->schema(function () use ($componentGroup) {
                        return
                            $this->components->filter(fn(Component $component) => $componentGroup->is($component->group))
                                ->map(fn (Component $component) => Group::make([
                                    ToggleButtons::make($componentGroup->id . '.components.' . $component->id . '.status')
                                        ->label($component->name)
                                        ->inline()
                                        ->live()
                                        ->options(ComponentStatusEnum::class)
                                        ->afterStateUpdated(function (ComponentStatusEnum $state) use ($component) {
                                            return $component->update(['status' => $state]);
                                        })
                            ]))->toArray();
                    })
                    ->collapsed($componentGroup->isCollapsible());
            })->toArray();

        return $form->schema($schema)->statePath('formData');
    }

    protected function loadVisibleComponentGroups(): Collection
    {
        return ComponentGroup::query()
            ->select(['id', 'name', 'collapsed', 'visible'])
            ->where('visible', '=', true)
            ->get()
            ->push(ComponentGroup::query()->make(ComponentGroup::defaultGroup()));
    }
}
