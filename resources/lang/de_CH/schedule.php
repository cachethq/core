<?php

return [
    'resource_label' => 'Wartung|Wartungen',
    'list' => [
        'headers' => [
            'name' => 'Name',
            'status' => 'Status',
            'scheduled_at' => 'Beginn',
            'completed_at' => 'Abgeschlossen am',
            'published_at' => 'Veröffentlicht am',
            'created_at' => 'Erstellt am',
            'updated_at' => 'Aktualisiert am',
            'deleted_at' => 'Gelöscht am',
        ],
        'published_now' => 'Veröffentlicht',
        'filters' => [
            'scheduled' => 'Geplant (noch nicht veröffentlicht)',
        ],
        'empty_state' => [
            'heading' => 'Wartungen',
            'description' => 'Plane deine Wartungen.',
        ],
        'actions' => [
            'record_update' => 'Update aufzeichnen',
            'complete' => 'Wartung abschliessen',
        ],
    ],
    'form' => [
        'name_label' => 'Name',
        'message_label' => 'Nachricht',
        'scheduled_at_label' => 'Beginn',
        'scheduled_at_helper' => 'Wann das Wartungsfenster beginnt.',
        'completed_at_label' => 'Ende',
        'completed_at_helper' => 'Wann das Wartungsfenster voraussichtlich endet.',
        'published_at_label' => 'Veröffentlicht am',
        'published_at_helper' => 'Plane die Wartung so, dass sie zu einem späteren Zeitpunkt auf der Statusseite erscheint. Bis dahin für alle verborgen. Leer lassen, um sofort zu veröffentlichen.',
        'notify_subscribers_label' => 'Abonnenten über diese Wartung benachrichtigen.',
        'notifications_helper' => 'Abonnenten per E-Mail über diese geplante Wartung und ihre Updates informieren.',
        'meta_label' => 'Metadaten',
        'add_component' => [
            'action_label' => 'Komponente hinzufügen',
            'header' => 'Betroffene Komponenten',
            'component_label' => 'Komponente',
            'status_label' => 'Status während der Wartung',
        ],
    ],
    'add_update' => [
        'new_update_label' => 'Neues Update',
        'success_title' => 'Update aufgezeichnet',
        'success_body' => 'Du hast ein neues Update zu :name veröffentlicht.',
        'form' => [
            'message_label' => 'Nachricht',
            'completed_at_label' => 'Abgeschlossen am',
        ],
    ],
    'status' => [
        'upcoming' => 'Demnächst',
        'in_progress' => 'Läuft',
        'complete' => 'Abgeschlossen',
    ],
    'planned_maintenance_header' => 'Geplante Wartung',
];
