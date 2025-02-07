<?php

return [
    'resource_label' => 'Statistiek|Statistieken',
    'list' => [
        'headers' => [
            'name' => 'Naam',
            'suffix' => 'Achtervoegsel',
            'default_value' => 'Standaardwaarde',
            'calc_type' => 'Metrisch type',
            'display_chart' => 'Diagram weergeven',
            'places' => 'Plaatsen',
            'default_view' => 'Standaard weergave',
            'threshold' => 'Drempelwaarde',
            'order' => 'Serie',
            'visible' => 'Zichtbaar',
            'points_count' => 'Scoren',
            'created_at' => 'Gemaakt op',
            'updated_at' => 'Bijgewerkt op',
        ],
        'empty_state' => [
            'heading' => 'Statistieken',
            'description' => 'Metrieken worden gebruikt om gegevens te volgen en weer te geven.',
        ],
    ],
    'form' => [
        'name_label' => 'Name',
        'suffix_label' => 'Achtervoegsel',
        'description_label' => 'Beschrijving',
        'default_view_label' => 'Standaard weergave',
        'default_value_label' => 'Standaardwaarde',
        'calc_type_label' => 'Metrisch type',
        'places_label' => 'Plaatsen',
        'threshold_label' => 'Drempelwaarde',

        'visible_label' => 'Zichtbaar',
        'display_chart_label' => 'Diagram weergeven',
    ],
    'overview' => [
        'metric_points_label' => 'Metrische punten',
        'metric_points_description' => 'Huidige metrische punten.',
    ],
    'sum_label' => 'Som',
    'average_label' => 'Gemiddeld',
    'view_labels' => [
        'last_hour' => 'Laatste Uur',
        'today' => 'Vandaag',
        'week' => 'Week',
        'month' => 'Maand',
    ],
];
