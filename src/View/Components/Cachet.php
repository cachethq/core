<?php

namespace Cachet\View\Components;

use Cachet\Settings\AppSettings;
use Cachet\Settings\CustomizationSettings;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Str;

class Cachet extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        private readonly AppSettings $appSettings,
        private readonly CustomizationSettings $customizationSettings,
        private ?string $title = null,
        private ?string $description = null
    ) {
        if ($this->title) {
            $this->title .= ' - '.($this->appSettings->name ?: config('cachet.title'));
        }

        $this->title ??= ($this->appSettings->name ?? config('cachet.title'));
        $this->description ??= (Str::of($this->appSettings->about)->trim()->toString());
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('cachet::components.cachet', [
            'title' => $this->title,
            'description' => $this->description,
            'site_name' => $this->appSettings->name,
            'cachet_header' => $this->customizationSettings->header,
            'cachet_css' => $this->customizationSettings->stylesheet,
            'cachet_footer' => $this->customizationSettings->footer,
            'refresh_rate' => $this->appSettings->refresh_rate,
        ]);
    }
}
