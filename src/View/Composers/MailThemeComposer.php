<?php

namespace Cachet\View\Composers;

use Cachet\Data\Cachet\ThemeData;
use Cachet\Settings\AppSettings;
use Cachet\Settings\ThemeSettings;
use Illuminate\View\View;

class MailThemeComposer
{
    public function __construct(
        private readonly AppSettings $appSettings,
        private readonly ThemeSettings $themeSettings,
    ) {}

    /**
     * Provide the status page theme to every Cachet mail view.
     */
    public function compose(View $view): void
    {
        $view->with([
            'appName' => $this->appSettings->name ?? config('cachet.title'),
            'colors' => (new ThemeData($this->themeSettings))->lightColors(),
        ]);
    }
}
