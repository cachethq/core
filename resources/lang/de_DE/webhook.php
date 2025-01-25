<?php

return [
    'resource_label' => 'Webhook|Webhooks',
    'event_selection' => [
        'all' => 'Alle Ereignisse senden',
        'selected' => 'Nur ausgewählte Ereignisse senden',
    ],
    'form' => [
        'url_label' => 'Payload URL',
        'url_helper' => 'Ereignisse werden an diese URL gesendet.',
        'secret_label' => 'Geheimschlüssel',
        'secret_helper' => 'Das Payload wird mit diesem Geheimnis signiert. Weitere Informationen findest Du in der *Webhook-Dokumentation*.',
        'description_label' => 'Beschreibung',
        'event_selection_label' => 'Alle Ereignisse senden?',
        'events_label' => 'Ereignisse',
        'edit_secret_label' => 'Geheimschlüssel bearbeiten',
        'update_secret_label' => 'Geheimschlüssel aktualisieren',
    ],
    'attempts' => [
        'heading' => 'Versuche',
        'empty_state' => 'Es wurden noch keine Versuche für diesen Webhook unternommen.',
    ],
    'list' => [
        'headers' => [
            'url' => 'Payload URL',
            'success_rate_24h' => 'Erfolgsrate (24h)',
        ],
    ],
];
