<?php

return [
    'level' => [
        'admin' => 'Beheerder',
        'user' => 'Gebruiker',
    ],
    'resource_label' => 'Gebruiker|Gebruikers',
    'list' => [
        'headers' => [
            'name' => 'Naam',
            'email' => 'E-mailadres',
            'email_verified_at' => 'E-mail geverifieerd op',
            'is_admin' => 'Is beheerder?',
        ],
        'actions' => [
            'verify_email' => 'E-mailadres verifiëren',
        ],
    ],
    'form' => [
        'name_label' => 'Naam',
        'email_label' => 'E-mailadres',
        'password_label' => 'Wachtwoord',
        'password_confirmation_label' => 'Bevestig wachtwoord',
        'is_admin_label' => 'Beheerder',
    ],
];
