<?php

namespace Cachet\View\Components;

use Cachet\Settings\AppSettings;
use Cachet\Settings\CustomizationSettings;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Cachet extends Component
{
    public string $title;

    /**
     * Create a new component instance.
     */
    public function __construct(
        private AppSettings $appSettings,
        private CustomizationSettings $customizationSettings,
    ) {
        $this->title ??= $this->appSettings->name ?? config('cachet.title');
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('cachet::components.cachet', [
            'cachet_header' => $this->customizationSettings->header,
            'cachet_footer' => $this->customizationSettings->footer,
            'refresh_rate' => $this->appSettings->refresh_rate,
        ]);
    }
}
