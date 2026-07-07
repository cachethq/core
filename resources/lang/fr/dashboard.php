<?php

return [
    'system_health' => [
        'empty' => 'Aucun composant n’a encore été configuré.',
        'add_component' => 'Ajouter un composant',
    ],
    'open_incidents' => [
        'heading' => 'Incidents ouverts',
        'headers' => [
            'name' => 'Nom',
            'status' => 'Statut',
            'occurred_at' => 'Survenu le',
            'components' => 'Composants affectés',
        ],
        'actions' => [
            'create' => 'Créer un incident',
        ],
        'empty_state' => [
            'heading' => 'Aucun incident ouvert',
            'description' => 'Tout va bien. Les incidents qui n’ont pas été résolus s’afficheront ici.',
        ],
    ],
    'upcoming_maintenance' => [
        'heading' => 'Maintenance',
        'headers' => [
            'name' => 'Nom',
            'status' => 'Statut',
            'scheduled_at' => 'Planifié le',
        ],
        'actions' => [
            'create' => 'Planifier une maintenance',
        ],
        'empty_state' => [
            'heading' => 'Aucune maintenance planifiée',
            'description' => 'Les maintenances à venir et en cours s’afficheront ici.',
        ],
    ],
];
