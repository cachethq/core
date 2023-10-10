<?php

use Workbench\Database\Factories\UserFactory;

it('can render the login screen', function () {
    $response = $this->get('/status/login');

    $response->assertOk();
});

it('can authenticate a user', function () {
    $user = UserFactory::new()->create();

    $response = $this->post('/status/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect('/status/dashboard');
});

it('cannot authenticate a user with invalid credentials', function () {
    $user = UserFactory::new()->create();

    $this->post('/status/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

it('can logout a user', function () {
    $user = UserFactory::new()->create();

    $this->actingAs($user);

    $response = $this->post('/status/logout');

    $this->assertGuest();
    $response->assertRedirect('/status/login');
});
