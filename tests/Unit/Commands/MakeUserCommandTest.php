<?php

it('creates the admin user', function () {
    $this->artisan('cachet:make:user', [
        'email' => 'dan@example.com',
        '--name' => 'Dan',
        '--admin' => true,
        '--password' => 'password',
    ]);

    $this->assertDatabaseHas('users', [
        'email' => 'dan@example.com',
        'name' => 'Dan',
        'is_admin' => true,
    ]);
});

it('creates the standard user', function () {
    $this->artisan('cachet:make:user', [
        'email' => 'dan@example.com',
        '--name' => 'Dan',
        '--admin' => false,
        '--password' => 'password',
    ]);

    $this->assertDatabaseHas('users', [
        'email' => 'dan@example.com',
        'name' => 'Dan',
        'is_admin' => false,
    ]);
});

it('creates the standard user using prompts', function () {
    $this->artisan('cachet:make:user')
        ->expectsQuestion('What is the user\'s name?', 'Dan')
        ->expectsQuestion('What is the user\'s email?', 'dan@example.com')
        ->expectsConfirmation('Is the user an admin?', 'No')
        ->expectsQuestion('What is the user\'s password?', 'password');

    $this->assertDatabaseHas('users', [
        'email' => 'dan@example.com',
        'name' => 'Dan',
        'is_admin' => false,
    ]);
});

it('creates the admin user using prompts', function () {
    $this->artisan('cachet:make:user')
        ->expectsQuestion('What is the user\'s name?', 'Dan')
        ->expectsQuestion('What is the user\'s email?', 'dan@example.com')
        ->expectsConfirmation('Is the user an admin?', 'Yes')
        ->expectsQuestion('What is the user\'s password?', 'password');

    $this->assertDatabaseHas('users', [
        'email' => 'dan@example.com',
        'name' => 'Dan',
        'is_admin' => true,
    ]);
});
