<?php

namespace Tests\Unit\Mail;

use Cachet\Data\Cachet\ThemeData;
use Cachet\Mail\TestMail;
use Cachet\Settings\AppSettings;
use Cachet\Settings\ThemeSettings;
use Cachet\View\Composers\MailThemeComposer;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Mime\Email;

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

it('embeds the configured app logo', function () {
    Storage::fake('public');
    Storage::disk('public')->put('app-logo.png', (string) file_get_contents(CACHET_PATH.'public/logo.png'));

    $theme = app(ThemeSettings::class);
    $theme->app_banner = 'app-logo.png';
    $theme->save();

    config()->set('mail.mailers.array', ['transport' => 'array']);

    $sentMessage = Mail::mailer('array')->to('subscriber@example.com')->send(new TestMail);
    $email = $sentMessage?->getSymfonySentMessage()->getOriginalMessage();

    expect($email)->toBeInstanceOf(Email::class)
        ->and($email->getAttachments())->toHaveCount(1);

    $logo = $email->getAttachments()[0];

    expect($logo->getDisposition())->toBe('inline')
        ->and($logo->getFilename())->toBe('app-logo.png')
        ->and($logo->getContentType())->toBe('image/png')
        ->and($email->getHtmlBody())->toContain('src="cid:'.$logo->getContentId().'"');
});
