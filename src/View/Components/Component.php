<?php

namespace Cachet\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component as ViewComponent;

class Component extends ViewComponent
{
    /**
     * Create a new component instance.
     */
    public function __construct(public \Cachet\Models\Component $component)
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('cachet::components.component', [
            'status' => $this->component->status,
        ]);
    }
}
