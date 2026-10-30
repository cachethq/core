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
            'checked' => 'Überwacht',
            'checked_at' => 'Geprüft am',
            'updated_at' => 'Aktualisiert am',
            'deleted_at' => 'Gelöscht am',
        ],
        'empty_state' => [
            'heading' => 'Komponenten',
            'description' => 'Komponenten stellen die verschiedenen Teile deines Systems dar, die den Status deiner Statusseite beeinflussen können.',
        ],
        'ungrouped' => 'Komponenten ohne Gruppe',
    ],
    'attach' => [
        'heading' => 'Komponenten zuordnen',
        'placeholder' => 'Komponenten auswählen',
    ],
    'last_updated' => 'Letzte Aktualisierung :timestamp',
    'description_label' => 'Über :component',
    'view_details' => 'Details anzeigen',
    'form' => [
        'name_label' => 'Name',
        'status_label' => 'Status',
        'description_label' => 'Beschreibung',
        'component_group_label' => 'Komponentengruppe',
        'link_label' => 'Link',
        'link_helper' => 'Ein optionaler Link zur Komponente.',
        'availability_section_title' => 'Verfügbarkeit',
        'enabled_label' => 'Aktiviert',
        'checked_label' => 'Überwachung',
        'checked_helper' => 'Diese Komponente regelmässig prüfen und ihre Verfügbarkeit erfassen.',
    ],
    'status' => [
        'operational' => 'Funktionsfähig',
        'performance_issues' => 'Leistungsprobleme',
        'partial_outage' => 'Teilausfall',
        'major_outage' => 'Schwerer Ausfall',
        'under_maintenance' => 'Wartungsarbeiten',
        'unknown' => 'Unbekannt',
    ],
    'status_source' => [
        'manual' => 'Manuell',
        'monitor' => 'Überwachung',
        'import' => 'Import',
        'system' => 'System',
    ],
    'overview' => [
        'operational_components_label' => 'Funktionsfähige Komponenten',
        'operational_components_description' => 'Komponenten, die voll funktionsfähig sind.',
    ],
    'checks' => [
        'title' => 'Letzte Prüfungen',
        'empty_state' => [
            'heading' => 'Noch keine Prüfungen',
            'description' => 'Prüfungen erscheinen hier, sobald die Komponente überwacht wurde.',
        ],
        'headers' => [
            'status' => 'Status',
            'successful' => 'Erfolgreich',
            'response_code' => 'Antwortcode',
            'response_time' => 'Antwortzeit',
            'checked_at' => 'Geprüft am',
        ],
    ],

];
