<?php

namespace Cachet\View\Components;

use Cachet\Settings\AppSettings;
use Cachet\Settings\CustomizationSettings;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Cachet extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        private AppSettings $appSettings,
        private CustomizationSettings $customizationSettings,
        private ?string $title = null
    ) {
        if ($this->title) {
            $this->title = $this->title.' - '.($this->appSettings->name ?: config('cachet.title'));
        }

        $this->title ??= ($this->appSettings->name ?? config('cachet.title'));
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('cachet::components.cachet', [
            'title' => $this->title,
            'cachet_header' => $this->customizationSettings->header,
            'cachet_css' => $this->customizationSettings->stylesheet,
            'cachet_footer' => $this->customizationSettings->footer,
            'refresh_rate' => $this->appSettings->refresh_rate,
        ]);
    }
}
