<?php

namespace Cachet\View\Components;

use Cachet\Cachet;
use Cachet\Settings\AppSettings;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Footer extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(private AppSettings $appSettings)
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('cachet::components.footer', [
            'showSupport' => $this->appSettings->show_support,
            'cachetVersion' => Cachet::version(),
            'showTimezone' => $this->appSettings->show_timezone,
            'timezone' => $this->appSettings->timezone,
        ]);
    }
}
