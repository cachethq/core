<?php

return [
    'level' => [
        'admin' => 'Admin',
        'user' => 'Benutzer',
    ],
    'resource_label' => 'Benutzer|Benutzer',
    'list' => [
        'headers' => [
            'name' => 'Name',
            'email' => 'Email-Addresse',
            'email_verified_at' => 'Email verifiziert am',
            'is_admin' => 'Administrator',
        ],
        'actions' => [
            'verify_email' => 'Email verifizieren',
        ],
    ],
    'form' => [
        'name_label' => 'Name',
        'email_label' => 'Email-Addresse',
        'password_label' => 'Passwort',
        'password_confirmation_label' => 'Passwort bestätigen',
        'preferred_locale' => 'Bevorzugte Sprache',
        'is_admin_label' => 'Administrator',
    ],
];
