<?php

namespace Cachet\View\Components;

use Cachet\Settings\CustomizationSettings;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Cachet extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(private CustomizationSettings $customizationSettings)
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('cachet::components.cachet', [
            'cachet_header' => $this->customizationSettings->header,
            'cachet_footer' => $this->customizationSettings->footer,
        ]);
    }
}
