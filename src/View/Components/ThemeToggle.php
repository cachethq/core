<?php

namespace Cachet\View\Components;

use Cachet\Settings\ThemeSettings;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ThemeToggle extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(private readonly ThemeSettings $themeSettings)
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('cachet::components.theme-toggle', [
            'themeMode' => $this->themeSettings->theme_mode,
        ]);
    }
}
