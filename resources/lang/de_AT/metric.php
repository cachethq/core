<?php

return [
    'resource_label' => 'Metrik|Metriken',
    'list' => [
        'headers' => [
            'name' => 'Name',
            'suffix' => 'Suffix',
            'default_value' => 'Standardwert',
            'calc_type' => 'Metrikentyp',
            'display_chart' => 'Diagramm anzeigen',
            'places' => 'Orte',
            'default_view' => 'Standardansicht',
            'threshold' => 'Schwelle',
            'order' => 'Reihenfolge',
            'visible' => 'Sichtbar',
            'points_count' => 'Punktezählung',
            'created_at' => 'Erstellt am',
            'updated_at' => 'Aktualisiert am',
        ],
        'empty_state' => [
            'heading' => 'Metriken',
            'description' => 'Zur Verfolgung und Anzeige von Daten werden Metriken verwendet.',
        ],
    ],
    'form' => [
        'name_label' => 'Name',
        'suffix_label' => 'Suffix',
        'description_label' => 'Beschreibung',
        'default_view_label' => 'Standardansicht',
        'default_value_label' => 'Standardwert',
        'calc_type_label' => 'Metrikentyp',
        'places_label' => 'Orte',
        'threshold_label' => 'Schwelle',

        'visible_label' => 'Sichtbar',
        'display_chart_label' => 'Diagramm anzeigen',
        'show_when_empty_label' => 'Anzeigen, wenn leer',
    ],
    'overview' => [
        'metric_points_label' => 'Metrische Punkte',
        'metric_points_description' => 'Aktuelle metrische Punkte.',
    ],
    'sum_label' => 'Summe',
    'average_label' => 'Durchschnitt',
    'view_labels' => [
        'last_hour' => 'Letzte Stunde',
        'today' => 'Heute',
        'week' => 'Woche',
        'month' => 'Monat',
    ],
];
