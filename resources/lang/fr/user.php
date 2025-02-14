<?php

return [
    'level' => [
        'admin' => 'Administrateur',
        'user' => 'Utilisateur',
    ],
    'resource_label' => 'Utilisateur|Utilisateurs',
    'list' => [
        'headers' => [
            'name' => 'Nom',
            'email' => 'Adresse e-mail',
            'email_verified_at' => 'E-mail vérifié le',
            'is_admin' => 'Administrateur ?',
        ],
        'actions' => [
            'verify_email' => 'Vérifier l’e-mail',
        ],
    ],
    'form' => [
        'name_label' => 'Nom',
        'email_label' => 'Adresse e-mail',
        'password_label' => 'Mot de passe',
        'password_confirmation_label' => 'Confirmer le mot de passe',
        'preferred_locale' => 'Langue préférée',
        'preferred_locale_system_default' => 'Valeur par défaut du système',
        'is_admin_label' => 'Administrateur',
    ],
];
