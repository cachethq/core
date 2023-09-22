<?php

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Notification;
use Workbench\Database\Factories\UserFactory;

it('can render the reset password screen', function () {
    $response = $this->get('/status/forgot-password');

    $response->assertOk();
});

test('reset password link can be requested', function () {
    Notification::fake();

    $user = UserFactory::new()->create();

    $this->post('/status/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class);
});
