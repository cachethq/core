<?php

namespace Cachet\Filament\Widgets;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Models\Component;
use Cachet\Models\ComponentGroup;
use Filament\Forms\Components\Component as FilamentFormComponent;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Components extends Widget implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'cachet::filament.widgets.components';

    protected int|string|array $columnSpan = 'full';

    public Collection $formData;
    public  Collection $componentGroups;

    public function mount(): void
    {
        $this->componentGroups = ComponentGroup::query()
            ->select(['id', 'name', 'collapsed', 'visible'])
            ->where('visible', '=', true)
            ->with('components', function (HasMany $query) {
                return $query->select(['id', 'component_group_id', 'name', 'status'])->where('enabled', '=', true);
            })
            ->get();

        $this->formData = $this->componentGroups->mapWithKeys(function (ComponentGroup $componentGroup) {
            $components = $componentGroup->components->mapWithKeys(function (Component $component) {
                return [$component->id => $component->only('status')];
            });
            return [$componentGroup->id => ['components' => $components]];
        });
    }

    public function form(Form $form): Form
    {
        $schema = $this->componentGroups
            ->map(function (ComponentGroup $componentGroup): FilamentFormComponent {
                return Section::make($componentGroup->name)
                    ->schema(function () use ($componentGroup) {
                        return
                            $componentGroup->components->map(function (Component $component) use ($componentGroup) {
                                return
                                    Group::make([
                                        Radio::make($componentGroup->id . '.components.' . $component->id . '.status')
                                            ->label($component->name)
                                            ->columns(count(ComponentStatusEnum::cases()))
                                            ->live()
                                            ->options(ComponentStatusEnum::class)
                                            ->afterStateUpdated(function (ComponentStatusEnum $state) use ($component) {
                                                return $component->update(['status' => $state]);
                                            })
                                    ]);
                            })->toArray();
                    })
                    ->collapsed($componentGroup->isCollapsible());
            })->toArray();

        return $form->schema($schema)->statePath('formData');
    }
}
