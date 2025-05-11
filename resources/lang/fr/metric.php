<?php

return [
    'resource_label' => 'Métrique|Métriques',
    'list' => [
        'headers' => [
            'name' => 'Nom',
            'suffix' => 'Suffixe',
            'default_value' => 'Valeur par défaut',
            'calc_type' => 'Type de métrique',
            'display_chart' => 'Afficher le graphique',
            'places' => 'Décimales',
            'default_view' => 'Vue par défaut',
            'threshold' => 'Seuil',
            'order' => 'Ordre',
            'visible' => 'Visible',
            'points_count' => 'Nombre de points',
            'created_at' => 'Créé le',
            'updated_at' => 'Mis à jour le',
        ],
        'empty_state' => [
            'heading' => 'Métriques',
            'description' => 'Les métriques sont utilisées pour suivre et afficher des données.',
        ],
    ],
    'form' => [
        'name_label' => 'Nom',
        'suffix_label' => 'Suffixe',
        'description_label' => 'Description',
        'default_view_label' => 'Vue par défaut',
        'default_value_label' => 'Valeur par défaut',
        'calc_type_label' => 'Type de métrique',
        'places_label' => 'Décimales',
        'threshold_label' => 'Seuil',
        'visible_label' => 'Visible',
        'display_chart_label' => 'Afficher le graphique',
    ],
    'overview' => [
        'metric_points_label' => 'Points de métrique',
        'metric_points_description' => 'Points de métrique récents.',
    ],
    'sum_label' => 'Somme',
    'average_label' => 'Moyenne',
    'view_labels' => [
        'last_hour' => 'Dernière heure',
        'today' => 'Aujourd’hui',
        'week' => 'Semaine',
        'month' => 'Mois',
    ],
];
