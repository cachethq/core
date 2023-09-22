<?php

declare(strict_types=1);

namespace Cachet\View\Components;

use Cachet\Cachet;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Footer extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('cachet::components.footer', [
            'cachetVersion' => Cachet::version(),
        ]);
    }
}
