<?php

return [
    'resource_label' => 'Komponentengruppe|Komponentengruppen',
    'incident_count' => ':count Vorfall|:count Vorfälle',
    'visibility' => [
        'expanded' => 'Immer ausgeklappt',
        'collapsed' => 'Immer eingeklappt',
        'collapsed_unless_incident' => 'Eingeklappt - außer bei aktivem Vorfall',
    ],
    'list' => [
        'headers' => [
            'name' => 'Name',
            'visible' => 'Sichtbar',
            'collapsed' => 'Ausgeklappt',
            'created_at' => 'Erstellt am',
            'updated_at' => 'Aktualisiert am',
        ],
        'empty_state' => [
            'heading' => 'Komponentengruppen',
            'description' => 'Gruppiere verwandte Komponenten',
        ],
    ],
    'form' => [
        'name_label' => 'Name',
        'visible_label' => 'Sichtbar',
        'collapsed_label' => 'Ausgeklappt',
    ],
];
