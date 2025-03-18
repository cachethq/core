<?php

return [
    'resource_label' => 'Métrica|Métricas',
    'list' => [
        'headers' => [
            'name' => 'Nombre',
            'suffix' => 'Sufijo',
            'default_value' => 'Valor por Defecto',
            'calc_type' => 'Tipo de Métrica',
            'display_chart' => 'Mostrar Gráfico',
            'places' => 'Decimales',
            'default_view' => 'Vista Predeterminada',
            'threshold' => 'Umbral',
            'order' => 'Orden',
            'visible' => 'Visible',
            'points_count' => 'Número de Puntos',
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
        'default_view_label' => 'Vista Predeterminada',
        'default_value_label' => 'Valor por Defecto',
        'calc_type_label' => 'Tipo de Métrica',
        'places_label' => 'Decimales',
        'threshold_label' => 'Umbral',
        
        'visible_label' => 'Visible',
        'display_chart_label' => 'Mostrar Gráfico',
    ],
    'overview' => [
        'metric_points_label' => 'Puntos de la Métrica',
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
