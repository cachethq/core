<?php

namespace Cachet\View\Components;

use Cachet\Enums\ScheduleStatusEnum;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ScheduleUpdateStatus extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public readonly ?ScheduleStatusEnum $status = null)
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('cachet::components.schedule-update-status', [
            'title' => $this->status?->getLabel(),
            'color' => $this->status?->getColor() ?? 'gray',
            'icon' => $this->status?->getIcon(),
        ]);
    }
}
