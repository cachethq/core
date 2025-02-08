<?php

return [
    'resource_label' => 'Schema|Schema\'s',
    'list' => [
        'headers' => [
            'name' => 'Naam',
            'status' => 'Toestand',
            'scheduled_at' => 'Gepland voor',
            'completed_at' => 'Voltooid op',
            'created_at' => 'Gemaakt op',
            'updated_at' => 'Bijgewerkt op',
            'deleted_at' => 'Verwijderd op',
        ],
        'empty_state' => [
            'heading' => 'Schema\'s',
            'description' => 'Plan en organiseer uw onderhoud.',
        ],
        'actions' => [
            'record_update' => 'Update publiceren',
            'complete' => 'Onderhoud afronden',
        ],
    ],
    'form' => [
        'name_label' => 'Naam',
        'message_label' => 'Nieuws',
        'scheduled_at_label' => 'Gepland voor',
        'completed_at_label' => 'Voltooid op',
    ],
    'add_update' => [
        'success_title' => 'Schema bijgewerkt - :name',
        'success_body' => 'Er is een update van het schema uitgebracht.',
        'form' => [
            'message_label' => 'Nieuws',
            'completed_at_label' => 'Voltooid op',
        ],
    ],
    'status' => [
        'upcoming' => 'Gepland',
        'in_progress' => 'Actief',
        'complete' => 'Voltooid',
    ],
    'planned_maintenance_header' => 'Gepland onderhoud',
];
