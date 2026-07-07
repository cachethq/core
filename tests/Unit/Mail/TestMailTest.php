<?php

namespace Tests\Unit\Mail;

use Cachet\Data\Cachet\ThemeData;
use Cachet\Mail\TestMail;
use Cachet\Settings\AppSettings;
use Cachet\Settings\ThemeSettings;
use Cachet\View\Composers\MailThemeComposer;

it('renders the themed test email', function () {
    $html = (new TestMail)->render();

    expect($html)->toContain(__('cachet::settings.manage_notifications.test_email_heading'))
        ->toContain(route('cachet.status-page'))
        ->toContain(app(AppSettings::class)->name);
});

it('uses the configured theme accent color', function () {
    $theme = app(ThemeSettings::class);
    $theme->accent = 'purple';
    $theme->save();

    $accent = MailThemeComposer::hex((new ThemeData($theme))->lightColors()['accent']);

    expect((new TestMail)->render())->toContain($accent);
});
