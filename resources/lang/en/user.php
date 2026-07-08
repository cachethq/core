<?php

return [
    'level' => [
        'admin' => 'Admin',
        'user' => 'User',
    ],
    'resource_label' => 'User|Users',
    'list' => [
        'headers' => [
            'name' => 'Name',
            'email' => 'Email Address',
            'email_verified_at' => 'Email Verified At',
            'is_admin' => 'Is Admin?',
        ],
        'actions' => [
            'verify_email' => 'Verify Email',
            'reset_two_factor' => 'Reset Two-Factor Authentication',
            'reset_two_factor_confirmation' => 'This will remove the user\'s two-factor authentication setup and recovery codes. They will be able to sign in with only their password until they set it up again.',
            'reset_two_factor_success' => 'Two-factor authentication has been reset.',
        ],
    ],
    'form' => [
        'name_label' => 'Name',
        'email_label' => 'Email Address',
        'password_label' => 'Password',
        'password_confirmation_label' => 'Confirm Password',
        'preferred_locale' => 'Preferred Locale',
        'preferred_locale_system_default' => 'System Default',
        'is_admin_label' => 'Admin',
    ],
];
