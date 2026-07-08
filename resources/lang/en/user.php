<?php

return [
    'resource_label' => 'User|Users',
    'list' => [
        'headers' => [
            'name' => 'Name',
            'email' => 'Email address',
            'email_verified_at' => 'Email verified at',
            'is_admin' => 'Admin',
        ],
        'actions' => [
            'verify_email' => 'Verify email',
            'reset_two_factor' => 'Reset two-factor authentication',
            'reset_two_factor_confirmation' => 'This will remove the user\'s two-factor authentication setup and recovery codes. They will be able to sign in with only their password until they set it up again.',
            'reset_two_factor_success' => 'Two-factor authentication reset.',
        ],
    ],
    'form' => [
        'name_label' => 'Name',
        'email_label' => 'Email address',
        'password_label' => 'Password',
        'password_confirmation_label' => 'Confirm password',
        'preferred_locale' => 'Preferred locale',
        'preferred_locale_system_default' => 'System default',
        'is_admin_label' => 'Admin',
    ],
];
