<?php

use Cachet\Data\Cachet\ThemeData;
use Cachet\Settings\ThemeSettings;

it('compiles dark styles for both the class strategy and the system preference fallback', function () {
    $styles = (new ThemeData(app(ThemeSettings::class)))->styles;

    expect($styles)
        ->toContain(':root.dark')
        ->toContain('@media(prefers-color-scheme: dark)')
        ->toContain(':root:not(.light)');
});
