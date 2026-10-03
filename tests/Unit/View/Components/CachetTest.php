<?php

use Cachet\Settings\AppSettings;
use Cachet\View\Components\Cachet;

it('provides site identity metadata when page content is hidden', function () {
    $settings = app(AppSettings::class);
    $settings->name = 'Acme Status';
    $settings->about = 'A private production system.';
    $settings->show_site_name = false;
    $settings->show_about = false;
    $settings->save();

    $viewData = app(Cachet::class)->render()->getData();

    expect($viewData)
        ->title->toBe('Acme Status')
        ->description->toBe('A private production system.');
});
