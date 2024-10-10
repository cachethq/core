<?php

namespace Cachet\View\Components;

use Cachet\Settings\AppSettings;
use Cachet\Settings\ThemeSettings;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Header extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(private AppSettings $appSettings, private ThemeSettings $themeSettings)
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('cachet::components.header', [
            'siteName' => $this->appSettings->name,
            'appBanner' => $this->themeSettings->app_banner,
            'dashboardLoginLink' => $this->appSettings->dashboard_login_link,
        ]);
    }
}
