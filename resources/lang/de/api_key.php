<?php

return [
    'resource_label' => 'API Schlüssel|API Schlüssel',
    'show_token' => [
        'heading' => 'Dein API Token wurde generiert',
        'description' => 'Bitte kopiere deinen API Token. Aus Gründen der Sicherheit, wird dieser nicht erneut angezeigt.',
        'copy_tooltip' => 'Token kopiert!',
    ],
    'abilities_label' => ':ability :resource',
    'form' => [
        'name_label' => 'Token Name',
        'expires_at_label' => 'Abgelaufen am',
        'expires_at_helper' => 'Läuft ab um Mitternacht. Frei lassen für keinen Ablauf.',
        'expires_at_validation' => 'Das Ablaufdatum muss in der Zukunft liegen.',
        'abilities_label' => 'Berechtigungen',
        'abilities_hint' => 'Frei lassen, gibt dem Token volle Berechtigung.',
    ],
    'list' => [
        'actions' => [
            'revoke' => 'Widerrufen',
        ],
        'headers' => [
            'name' => 'Token Name',
            'abilities' => 'Berechtigungen',
            'created_at' => 'Erstellt am',
            'expires_at' => 'Läuft ab am',
            'updated_at' => 'Aktualisiert am',
        ],
    ],
];
