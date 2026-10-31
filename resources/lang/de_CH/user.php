<?php

return [
    'resource_label' => 'Benutzer|Benutzer',
    'profile_information_title' => 'Profilinformationen',
    'security_section_title' => 'Passwort und Sicherheit',
    'list' => [
        'headers' => [
            'name' => 'Name',
            'email' => 'E-Mail-Adresse',
            'email_verified_at' => 'E-Mail verifiziert am',
            'is_admin' => 'Administrator',
        ],
        'actions' => [
            'verify_email' => 'E-Mail verifizieren',
            'reset_two_factor' => 'Zwei-Faktor-Authentifizierung zurücksetzen',
            'reset_two_factor_confirmation' => 'Dadurch werden die Zwei-Faktor-Einrichtung und die Wiederherstellungscodes dieses Benutzers entfernt. Bis zur erneuten Einrichtung genügt zur Anmeldung das Passwort.',
            'reset_two_factor_success' => 'Zwei-Faktor-Authentifizierung zurückgesetzt.',
        ],
    ],
    'form' => [
        'name_label' => 'Name',
        'email_label' => 'E-Mail-Adresse',
        'password_label' => 'Passwort',
        'password_confirmation_label' => 'Passwort bestätigen',
        'preferred_locale' => 'Bevorzugte Sprache',
        'preferred_locale_system_default' => 'Systemstandard',
        'is_admin_label' => 'Administrator',
    ],
];
