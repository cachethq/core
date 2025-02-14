<?php

return [
    'resource_label' => 'Clé API|Clés API',
    'show_token' => [
        'heading' => 'Votre jeton API a été généré',
        'description' => 'Veuillez copier votre nouveau jeton API. Pour votre sécurité, il ne sera plus affiché.',
        'copy_tooltip' => 'Jeton copié !',
    ],
    'abilities_label' => ':ability :resource',
    'form' => [
        'name_label' => 'Nom du jeton',
        'expires_at_label' => 'Expire le',
        'expires_at_helper' => 'Expire à minuit. Laissez vide pour une durée illimitée',
        'expires_at_validation' => 'La date d\'expiration doit être dans le futur',
        'abilities_label' => 'Permissions',
        'abilities_hint' => 'Laisser vide accordera toutes les permissions au jeton',
    ],
    'list' => [
        'actions' => [
            'revoke' => 'Révoquer',
        ],
        'headers' => [
            'name' => 'Nom du jeton',
            'abilities' => 'Permissions',
            'created_at' => 'Créé le',
            'expires_at' => 'Expire le',
            'updated_at' => 'Mis à jour le',
        ],
    ],
];
