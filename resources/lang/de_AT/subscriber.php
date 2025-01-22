<?php

return [
    'resource_label' => 'Abonnent|Abonnenten',
    'list' => [
        'headers' => [
            'email' => 'Email',
            'verify_code' => 'Verifizierungscode',
            'global' => 'Global',
            'phone_number' => 'Telefonnummer',
            'slack_webhook_url' => 'Slack Webhook-URL',
            'verified_at' => 'Verifiziert am',
            'created_at' => 'Erstellt am',
            'updated_at' => 'Aktualisiert am',
        ],
        'empty_state' => [
            'heading' => 'Abonnenten',
            'description' => 'Abonnenten sind Personen, welche die Statusseite für Benachrichtigungen abonniert haben.',
        ],
        'actions' => [
            'verify_label' => 'Verifizieren',
        ],
    ],
    'form' => [
        'email_label' => 'Email',
        'verify_code_label' => 'Verifizierungscode',
        'verified_at_label' => 'Verifiziert am',
        'global_label' => 'Global',
    ],
    'overview' => [
        'total_subscribers_label' => 'Abonnenten insgesamt',
        'total_subscribers_description' => 'Anzahl aller Abonnenten',
    ],
];
