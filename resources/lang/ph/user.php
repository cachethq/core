<?php

return [
    'level' => [
        'admin' => 'Admin',
        'user' => 'User',
    ],
    'resource_label' => 'User|Users',
    'list' => [
        'headers' => [
            'name' => 'Pangalan',
            'email' => 'Email Address',
            'email_verified_at' => 'Email na Kumpirmado noong',
            'is_admin' => 'Admin ba?',
        ],
        'actions' => [
            'verify_email' => 'Kumpirmahin ang Email',
        ],
    ],
    'form' => [
        'name_label' => 'Pangalan',
        'email_label' => 'Email Address',
        'password_label' => 'Password',
        'password_confirmation_label' => 'Kumpirmahin ang Password',
        'preferred_locale' => 'Piniling Wika',
        'preferred_locale_system_default' => 'Default ng Sistema',
        'is_admin_label' => 'Admin',
    ],
];
