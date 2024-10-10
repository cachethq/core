<?php

namespace Cachet\View\Components;

use Cachet\Models\Schedule;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ScheduleBadge extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public readonly Schedule $schedule)
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('cachet::components.schedule-badge', [
            'label' => $this->schedule->status->getLabel(),
            'color' => $this->schedule->status->getColor(),
            'icon' => $this->schedule->status->getIcon(),
        ]);
    }
}
