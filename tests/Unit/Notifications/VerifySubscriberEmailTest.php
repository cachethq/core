<?php

namespace Tests\Unit\Notifications;

use Cachet\Cachet;
use Cachet\Models\Subscriber;
use Cachet\Notifications\VerifySubscriberEmail;
use Illuminate\Mail\Markdown;

it('renders the themed verification email', function () {
    $subscriber = Subscriber::factory()->create();

    $mail = (new VerifySubscriberEmail)->toMail($subscriber);

    $html = (new Markdown(app('view'), ['theme' => Cachet::MAIL_THEME]))
        ->render($mail->markdown, $mail->viewData)
        ->toHtml();

    expect($mail->theme)->toBe(Cachet::MAIL_THEME)
        ->and($html)->toContain(__('cachet::subscriber.mail.verify.heading'))
        ->toContain(e($mail->viewData['verificationUrl']))
        ->toContain(e($subscriber->unsubscribeUrl()));
});

it('signs the verification url for the subscriber', function () {
    $subscriber = Subscriber::factory()->create();

    $url = (new VerifySubscriberEmail)->toMail($subscriber)->viewData['verificationUrl'];

    expect($url)
        ->toContain('/subscribers/verify/'.$subscriber->getKey().'/'.sha1($subscriber->email))
        ->toContain('signature=')
        ->toContain('expires=');
});
