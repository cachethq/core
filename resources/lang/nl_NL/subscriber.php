<?php

return [
    'resource_label' => 'Abonnee|Abonnees',
    'list' => [
        'headers' => [
            'email' => 'E-mailadres',
            'verify_code' => 'Verificatiecode',
            'global' => 'Wereldwijd',
            'phone_number' => 'Telefoonnummer',
            'slack_webhook_url' => 'Slack Webhook-URL',
            'verified_at' => 'Geverifieerd op',
            'created_at' => 'Gemaakt op',
            'updated_at' => 'Bijgewerkt op',
        ],
        'empty_state' => [
            'heading' => 'Abonnees',
            'description' => 'Abonnees zijn mensen die zich hebben geabonneerd op de statuspagina voor meldingen.',
        ],
        'actions' => [
            'verify_label' => 'Verifiëren',
        ],
    ],
    'form' => [
        'email_label' => 'E-mailadres',
        'verify_code_label' => 'Verificatiecode',
        'verified_at_label' => 'Geverifieerd op',
        'global_label' => 'Wereldwijd',
    ],
    'overview' => [
        'total_subscribers_label' => 'Totaal aantal abonnees',
        'total_subscribers_description' => 'Aantal van alle abonnees',
    ],
];
