<?php

return [
    'resource_label' => 'Abonné|Abonnés',
    'list' => [
        'headers' => [
            'email' => 'E-mail',
            'verify_code' => 'Code de vérification',
            'global' => 'Global',
            'phone_number' => 'Numéro de téléphone',
            'slack_webhook_url' => 'URL Webhook Slack',
            'verified_at' => 'Vérifié le',
            'created_at' => 'Créé le',
            'updated_at' => 'Mis à jour le',
        ],
        'empty_state' => [
            'heading' => 'Abonnés',
            'description' => 'Les abonnés sont des personnes qui se sont inscrites à votre page de statut pour recevoir des notifications.',
        ],
        'actions' => [
            'verify_label' => 'Vérifier',
        ],
    ],
    'form' => [
        'email_label' => 'E-mail',
        'verify_code_label' => 'Code de vérification',
        'verified_at_label' => 'Vérifié le',
        'global_label' => 'Global',
    ],
    'overview' => [
        'total_subscribers_label' => 'Nombre total d’abonnés',
        'total_subscribers_description' => 'Nombre total d’abonnés.',
    ],
];
