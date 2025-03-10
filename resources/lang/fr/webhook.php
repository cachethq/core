<?php

return [
    'resource_label' => 'Webhook|Webhooks',
    'event_selection' => [
        'all' => 'Envoyer tous les événements',
        'selected' => 'Envoyer uniquement les événements sélectionnés',
    ],
    'form' => [
        'url_label' => 'URL de la charge utile',
        'url_helper' => 'Les événements seront envoyés en POST à cette URL.',
        'secret_label' => 'Secret',
        'secret_helper' => 'La charge utile sera signée avec ce secret. Consultez la *documentation des webhooks* pour plus d’informations.',
        'description_label' => 'Description',
        'event_selection_label' => 'Envoyer tous les événements ?',
        'events_label' => 'Événements',
        'edit_secret_label' => 'Modifier le secret',
        'update_secret_label' => 'Mettre à jour le secret',
    ],
    'attempts' => [
        'heading' => 'Tentatives',
        'empty_state' => 'Aucune tentative n’a encore été effectuée pour ce webhook',
    ],
    'list' => [
        'headers' => [
            'url' => 'URL de la charge utile',
            'success_rate_24h' => 'Taux de succès (24h)',
        ],
    ],
];
