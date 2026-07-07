<?php

return [
    'system_health' => [
        'empty' => 'Er zijn nog geen componenten geconfigureerd.',
        'add_component' => 'Component toevoegen',
    ],
    'open_incidents' => [
        'heading' => 'Openstaande incidenten',
        'headers' => [
            'name' => 'Naam',
            'status' => 'Toestand',
            'occurred_at' => 'Voorgekomen op',
            'components' => 'Getroffen componenten',
        ],
        'actions' => [
            'create' => 'Incident toevoegen',
        ],
        'empty_state' => [
            'heading' => 'Geen openstaande incidenten',
            'description' => 'Alles in orde. Incidenten die nog niet zijn opgelost, worden hier weergegeven.',
        ],
    ],
    'upcoming_maintenance' => [
        'heading' => 'Onderhoud',
        'headers' => [
            'name' => 'Naam',
            'status' => 'Toestand',
            'scheduled_at' => 'Gepland voor',
        ],
        'actions' => [
            'create' => 'Onderhoud plannen',
        ],
        'empty_state' => [
            'heading' => 'Geen gepland onderhoud',
            'description' => 'Aankomend en actief onderhoud wordt hier weergegeven.',
        ],
    ],
];
