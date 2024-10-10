<?php

namespace Cachet\View\Components;

use Cachet\Enums\IncidentStatusEnum;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class IncidentUpdateStatus extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public readonly ?IncidentStatusEnum $status = null)
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('cachet::components.incident-update-status', [
            'title' => $this->status?->getLabel(),
            'color' => $this->status?->getColor() ?? 'gray',
            'icon' => $this->status?->getIcon(),
        ]);
    }
}
