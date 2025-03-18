<?php

return [
    'resource_label' => 'Métrica|Métricas',
    'list' => [
        'headers' => [
            'name' => 'Nombre',
            'suffix' => 'Sufijo',
            'default_value' => 'Valor por defecto',
            'calc_type' => 'Tipo de métrica',
            'display_chart' => '¿Mostrar gráfico?',
            'places' => 'Decimales',
            'default_view' => 'Vista predeterminada',
            'threshold' => 'Umbral',
            'order' => 'Orden',
            'visible' => 'Visible',
            'points_count' => 'Cantidad de puntos',
            'created_at' => 'Creado el',
            'updated_at' => 'Actualizado el',
        ],
        'empty_state' => [
            'heading' => 'Métricas',
            'description' => 'Las métricas se utilizan para rastrear y mostrar datos.',
        ],
    ],
    'form' => [
        'name_label' => 'Nombre',
        'suffix_label' => 'Sufijo',
        'description_label' => 'Descripción',
        'default_view_label' => 'Vista predeterminada',
        'default_value_label' => 'Valor por defecto',
        'calc_type_label' => 'Tipo de métrica',
        'places_label' => 'Decimales',
        'threshold_label' => 'Umbral',
        'visible_label' => 'Visible',
        'display_chart_label' => 'Mostrar gráfico',
    ],
    'overview' => [
        'metric_points_label' => 'Puntos de la métrica',
        'metric_points_description' => 'Puntos recientes de la métrica.',
    ],
    'sum_label' => 'Suma',
    'average_label' => 'Promedio',
    'view_labels' => [
        'last_hour' => 'Última Hora',
        'today' => 'Hoy',
        'week' => 'Semana',
        'month' => 'Mes',
    ],
];
