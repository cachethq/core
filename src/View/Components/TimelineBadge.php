<?php

namespace Cachet\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TimelineBadge extends Component
{
    public function __construct(public readonly string $icon, public readonly null|string|array $color = null, public readonly string $label = "")
    {
        //
    }

    public function render(): View|Closure|string
    {
        return view('cachet::components.timeline-badge', [
            'color' => $this->color ?? 'gray',
            'label' => $this->label,
            'icon' => $this->icon,
        ]);
    }
}
