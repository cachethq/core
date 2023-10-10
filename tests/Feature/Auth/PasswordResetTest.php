<?php

use Illuminate\Auth\Notifications\ResetPassword;
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

test('password can be reset with valid token', function () {
    Notification::fake();

    $user = UserFactory::new()->create();

    $this->post('/status/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
        $response = $this->post('/status/reset-password', [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasNoErrors();

        return true;
    });
});
