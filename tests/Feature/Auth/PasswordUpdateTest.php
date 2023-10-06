<?php

use Illuminate\Support\Facades\Hash;
use Workbench\Database\Factories\UserFactory;

test('password can be updated', function () {
    $user = UserFactory::new()->create();

    $response = $this
        ->actingAs($user)
        ->from('/status/profile')
        ->put('/status/password', [
            'current_password' => 'password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->assertTrue(Hash::check('new-password', $user->refresh()->password));
})->todo();

test('correct password must be provided to update password', function () {
    $user = UserFactory::new()->create();

    $response = $this
        ->actingAs($user)
        ->from('/status/profile')
        ->put('/password', [
            'current_password' => 'wrong-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

    $response
        ->assertSessionHasErrors('current_password')
        ->assertRedirect('/profile');
})->todo();
