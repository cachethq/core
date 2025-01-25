<?php

return [
    'resource_label' => 'API-sleutel|API-sleutels',
    'show_token' => [
        'heading' => 'Uw API-token is gegenereerd',
        'description' => 'Kopieer uw API-token. Om veiligheidsredenen wordt dit niet meer weergegeven.',
        'copy_tooltip' => 'Token gekopieerd!',
    ],
    'abilities_label' => ':ability :resource',
    'form' => [
        'name_label' => 'Tokennaam',
        'expires_at_label' => 'Verlopen op',
        'expires_at_helper' => 'Verloopt om middernacht. Vrijlaten zonder vervaldatum.',
        'expires_at_validation' => 'De vervaldatum moet in de toekomst liggen.',
        'abilities_label' => 'Toestemmingen',
        'abilities_hint' => 'Als u dit veld leeg laat, krijgt het token volledige rechten.',
    ],
    'list' => [
        'actions' => [
            'revoke' => 'Herroepen',
        ],
        'headers' => [
            'name' => 'Tokennaam',
            'abilities' => 'Toestemmingen',
            'created_at' => 'Gemaakt op',
            'expires_at' => 'Verloopt op',
            'updated_at' => 'Bijgewerkt op',
        ],
    ],
];
