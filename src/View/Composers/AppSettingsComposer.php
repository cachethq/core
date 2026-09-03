<?php

namespace Cachet\View\Composers;

use Cachet\Settings\AppSettings;
use Illuminate\View\View;

class AppSettingsComposer
{
    public function __construct(private readonly AppSettings $appSettings) {}

    /**
     * Provide settings only to views owned by Cachet.
     */
    public function compose(View $view): void
    {
        $view->with('appSettings', $this->appSettings);
    }
}
