<?php

return [
    'system_health' => [
        'empty' => 'Es wurden noch keine Komponenten konfiguriert.',
        'add_component' => 'Komponente hinzufügen',
    ],
    'open_incidents' => [
        'heading' => 'Offene Vorfälle',
        'headers' => [
            'name' => 'Name',
            'status' => 'Status',
            'occurred_at' => 'Aufgetreten',
            'components' => 'Betroffene Komponenten',
        ],
        'actions' => [
            'create' => 'Vorfall erstellen',
        ],
        'empty_state' => [
            'heading' => 'Keine offenen Vorfälle',
            'description' => 'Alles in Ordnung. Vorfälle, die noch nicht behoben wurden, werden hier angezeigt.',
        ],
    ],
    'upcoming_maintenance' => [
        'heading' => 'Wartung',
        'headers' => [
            'name' => 'Name',
            'status' => 'Status',
            'scheduled_at' => 'Terminiert für',
        ],
        'actions' => [
            'create' => 'Wartung planen',
        ],
        'empty_state' => [
            'heading' => 'Keine geplante Wartung',
            'description' => 'Anstehende und laufende Wartungsfenster werden hier angezeigt.',
        ],
    ],
];
