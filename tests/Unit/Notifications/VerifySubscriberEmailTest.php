<?php

namespace Tests\Unit\Notifications;

use Cachet\Models\Subscriber;
use Cachet\Notifications\VerifySubscriberEmail;

it('renders the themed verification email', function () {
    $subscriber = Subscriber::factory()->create();

    $mail = (new VerifySubscriberEmail)->toMail($subscriber);

    $html = view($mail->view, $mail->viewData)->render();

    expect($html)->toContain(__('cachet::subscriber.mail.verify.heading'))
        ->toContain(e($mail->viewData['verificationUrl']));
});

it('signs the verification url for the subscriber', function () {
    $subscriber = Subscriber::factory()->create();

    $url = (new VerifySubscriberEmail)->toMail($subscriber)->viewData['verificationUrl'];

    expect($url)
        ->toContain('/subscribers/verify/'.$subscriber->getKey().'/'.sha1($subscriber->email))
        ->toContain('signature=')
        ->toContain('expires=');
});
