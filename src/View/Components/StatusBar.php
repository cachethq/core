<?php

namespace Cachet\View\Components;

use Cachet\Status;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component as ViewComponent;

class StatusBar extends ViewComponent
{
    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('cachet::components.status-bar', [
            ...app(Status::class)->current(),
        ]);
    }
}
