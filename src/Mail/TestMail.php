<?php

namespace Cachet\Mail;

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
        return new Content(
            view: 'cachet::mail.test',
            with: [
                'statusPageUrl' => route('cachet.status-page'),
            ],
        );
    }
}
