<?php

namespace Cachet\View\Components;

use Cachet\Enums\ComponentStatusEnum;
use Closure;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ComponentBadge extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public ComponentStatusEnum $type = ComponentStatusEnum::unknown)
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('cachet::components.component-badge', [
            'label' => $this->type->getLabel(),
            'color' => $this->type->getColor(),
            'icon' => $this->type->getIcon(),
        ]);
    }
}
