<?php

namespace Cachet\View\Components;

use Cachet\Models\IncidentUpdate;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class IncidentUpdateStatus extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public IncidentUpdate $update)
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('cachet::components.incident-update-status', [
            'color' => $this->update->status->getColor(),
            'icon' => $this->update->status->getIcon(),
        ]);
    }
}
