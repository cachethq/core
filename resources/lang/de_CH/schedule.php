<?php

return [
    'resource_label' => 'Zeitplan|Zeitpläne',
    'list' => [
        'headers' => [
            'name' => 'Name',
            'status' => 'Status',
            'scheduled_at' => 'Terminiert für',
            'completed_at' => 'Abgeschlossen am',
            'created_at' => 'Erstellt am',
            'updated_at' => 'Aktualisiert am',
            'deleted_at' => 'Gelöscht am',
        ],
        'empty_state' => [
            'heading' => 'Zeitpläne',
            'description' => 'Plane und terminiere deine Wartungen.',
        ],
        'actions' => [
            'record_update' => 'Update aufzeichnen',
            'complete' => 'Wartung abschließen',
        ],
    ],
    'form' => [
        'name_label' => 'Name',
        'message_label' => 'Nachricht',
        'scheduled_at_label' => 'Beginn',
        'completed_at_label' => 'Ende',
    ],
    'add_update' => [
        'success_title' => 'Zeitplan aktualisiert - :name',
        'success_body' => 'Ein Zeitplan-Update wurde veröffentlicht.',
        'form' => [
            'message_label' => 'Nachricht',
            'completed_at_label' => 'Abgeschlossen am',
        ],
    ],
    'status' => [
        'upcoming' => 'Demnächst',
        'in_progress' => 'Aktiv',
        'complete' => 'Abgeschlossen',
    ],
    'planned_maintenance_header' => 'Geplante Wartung',
];
