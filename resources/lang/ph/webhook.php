<?php

return [
    'resource_label' => 'Webhook|Webhooks',
    'event_selection' => [
        'all' => 'Ipadala ang lahat ng mga kaganapan',
        'selected' => 'Ipadala lamang ang mga napiling kaganapan',
    ],
    'form' => [
        'url_label' => 'Payload URL',
        'url_helper' => 'Ang mga kaganapan ay ipo-post sa URL na ito.',
        'secret_label' => 'Lihim',
        'secret_helper' => 'Ang payload ay pipirmahan gamit ang lihim na ito. Tingnan ang *webhook na dokumentasyon* para sa karagdagang impormasyon.',
        'description_label' => 'Paglalarawan',
        'event_selection_label' => 'Ipadala ba ang lahat ng mga kaganapan?',
        'events_label' => 'Mga Kaganapan',
        'edit_secret_label' => 'I-edit ang lihim',
        'update_secret_label' => 'I-update ang lihim',
    ],
    'attempts' => [
        'heading' => 'Mga Pagsubok',
        'empty_state' => 'Walang pagsubok na ginawa sa webhook na ito.',
    ],
    'list' => [
        'headers' => [
            'url' => 'Payload URL',
            'success_rate_24h' => 'Tagumpay na rate (24h)',
        ],
    ],
];
