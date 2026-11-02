<?php

return [
    'resource_label' => 'Métrica|Métricas',
    'list' => [
        'headers' => [
            'name' => 'Nome',
            'suffix' => 'Sufixo',
            'default_value' => 'Valor predefinido',
            'calc_type' => 'Tipo de métrica',
            'display_chart' => 'Mostrar gráfico',
            'places' => 'Casas decimais',
            'default_view' => 'Vista predefinida',
            'threshold' => 'Limiar',
            'order' => 'Ordem',
            'visible' => 'Visível',
            'points_count' => 'Número de pontos',
            'created_at' => 'Criado em',
            'updated_at' => 'Atualizado em',
        ],
        'empty_state' => [
            'heading' => 'Métricas',
            'description' => 'As métricas servem para acompanhar e apresentar dados.',
        ],
    ],
    'form' => [
        'name_label' => 'Nome',
        'suffix_label' => 'Sufixo',
        'description_label' => 'Descrição',
        'default_view_label' => 'Vista predefinida',
        'default_value_label' => 'Valor predefinido',
        'calc_type_label' => 'Tipo de métrica',
        'places_label' => 'Casas decimais',
        'threshold_label' => 'Limiar',

        'visible_label' => 'Visível',
        'display_chart_label' => 'Mostrar gráfico',
        'show_when_empty_label' => 'Mostrar quando vazio',
    ],
    'overview' => [
        'metric_points_label' => 'Pontos de métrica',
        'metric_points_description' => 'Pontos de métrica recentes.',
    ],
    'sum_label' => 'Soma',
    'average_label' => 'Média',
    'description_label' => 'Sobre :metric',
    'chart_label' => 'Gráfico de :metric',
    'view_labels' => [
        'last_hour' => 'Última hora',
        'today' => 'Hoje',
        'week' => 'Semana',
        'month' => 'Mês',
    ],
];
