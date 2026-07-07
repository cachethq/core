<?php

namespace Cachet\Mail;

use Cachet\Data\Cachet\ThemeData;
use Cachet\Settings\AppSettings;
use Cachet\Settings\ThemeSettings;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class TestMail extends Mailable
{
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('cachet::settings.manage_notifications.test_email_subject'),
        );
    }

    public function content(): Content
    {
        $theme = new ThemeData(app(ThemeSettings::class));

        return new Content(
            view: 'cachet::mail.test',
            with: [
                'appName' => app(AppSettings::class)->name ?? config('cachet.title'),
                'colors' => $theme->lightColors(),
                'statusPageUrl' => route('cachet.status-page'),
            ],
        );
    }
}
