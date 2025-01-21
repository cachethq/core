<?php

return [
    'resource_label' => 'Webhook|Webhooks',
    'event_selection' => [
        'all' => 'Stuur alle evenementen',
        'selected' => 'Alleen geselecteerde gebeurtenissen verzenden',
    ],
    'form' => [
        'url_label' => 'Payload URL',
        'url_helper' => 'Gebeurtenissen worden naar deze URL verzonden.',
        'secret_label' => 'Geheime sleutel',
        'secret_helper' => 'De payload is met dit geheim ondertekend. Zie de *Webhook-documentatie* voor meer informatie.',
        'description_label' => 'Beschrijving',
        'event_selection_label' => 'Alle gebeurtenissen verzenden?',
        'events_label' => 'evenementen',
        'edit_secret_label' => 'Bewerken Geheime sleutel',
        'update_secret_label' => 'Geheime sleutel bijwerken',
    ],
    'attempts' => [
        'heading' => 'Poging',
        'empty_state' => 'Er zijn nog geen pogingen gedaan voor deze webhook.',
    ],
    'list' => [
        'headers' => [
            'url' => 'Payload URL',
            'success_rate_24h' => 'Succespercentage (24 uur)',
        ],
    ],
];
