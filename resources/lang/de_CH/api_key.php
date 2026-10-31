<?php

return [
    'resource_label' => 'API-Schlüssel|API-Schlüssel',
    'show_token' => [
        'heading' => 'Dein API-Token wurde generiert',
        'description' => 'Bitte kopiere deinen API-Token. Aus Sicherheitsgründen wird er nicht erneut angezeigt.',
        'copy_tooltip' => 'Token kopiert!',
    ],
    'abilities_label' => ':ability :resource',
    'form' => [
        'name_label' => 'Token-Name',
        'expires_at_label' => 'Läuft ab am',
        'expires_at_helper' => 'Läuft um Mitternacht ab. Leer lassen, damit der Token nie abläuft.',
        'expires_at_validation' => 'Das Ablaufdatum muss in der Zukunft liegen.',
        'abilities_label' => 'Berechtigungen',
        'abilities_hint' => 'Leer lassen, um dem Token alle Berechtigungen zu geben.',
    ],
    'list' => [
        'actions' => [
            'revoke' => 'Widerrufen',
        ],
        'headers' => [
            'name' => 'Token-Name',
            'abilities' => 'Berechtigungen',
            'created_at' => 'Erstellt am',
            'expires_at' => 'Läuft ab am',
            'updated_at' => 'Aktualisiert am',
        ],
    ],
];
