<?php

namespace Cachet\View\Components;

use Cachet\Enums\IncidentStatusEnum;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class IncidentBadge extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public IncidentStatusEnum|string $type)
    {
        if (is_string($this->type)) {
            $this->type = IncidentStatusEnum::fromString($type);
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('cachet::components.incident-badge', [
            'label' => $this->type->getLabel(),
            'color' => $this->type->getColor(),
            'icon' => $this->type->getIcon(),
        ]);
    }
}
