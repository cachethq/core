<?php

return [
    'resource_label' => 'Komponente|Komponenten',
    'list' => [
        'headers' => [
            'name' => 'Name',
            'status' => 'Status',
            'order' => 'Reihenfolge',
            'group' => 'Gruppe',
            'enabled' => 'Aktiviert',
            'created_at' => 'Erstellt am',
            'updated_at' => 'Aktualisiert am',
            'deleted_at' => 'Gelöscht am',
        ],
        'empty_state' => [
            'heading' => 'Komponenten',
            'description' => 'Komponenten stellen die verschiedenen Teile Deines Systems dar, die den Status Deiner Statusseite beeinflussen können.',
        ],
    ],
    'last_updated' => 'Letzte Aktualisierung :timestamp',
    'view_details' => 'Details anzeigen',
    'form' => [
        'name_label' => 'Name',
        'status_label' => 'Status',
        'description_label' => 'Beschreibung',
        'component_group_label' => 'Komponentengruppe',
        'link_label' => 'Link',
        'link_helper' => 'Ein optionaler Link zur Komponente',
    ],
    'status' => [
        'operational' => 'Funktionsfähig',
        'performance_issues' => 'Leistungsprobleme',
        'partial_outage' => 'Teilausfall',
        'major_outage' => 'Schwerer Ausfall',
        'under_maintenance' => 'Wartungsarbeiten',
        'unknown' => 'Unbekannt',
    ],

];
