<?php

return [
    'resource_label' => 'Webhook|Webhooks',
    'event_selection' => [
        'all' => 'Send all events',
        'selected' => 'Only send selected events',
    ],
    'form' => [
        'url_label' => 'Payload URL',
        'url_helper' => 'Events will POST to this URL.',
        'secret_label' => 'Secret',
        'secret_helper' => 'The payload will be signed with this secret. See *webhook documentation* for more information.',
        'description_label' => 'Description',
        'event_selection_label' => 'Send all events?',
        'events_label' => 'Events',
    ],
    'attempts' => [
        'heading' => 'Attempts',
        'empty_state' => 'No attempts have been made to this webhook yet',
    ],
    'list' => [
        'headers' => [
            'url' => 'Payload URL',
            '24h_success_rate' => 'Success rate (24h)',
        ],
    ],
    'events' => [
        'beacon_sent' => 'Beacon sent',
        'component_created' => 'Component created',
        'component_updated' => 'Component updated',
        'component_deleted' => 'Component deleted',
        'component_status_changed' => 'Component status changed',
    ],
];
