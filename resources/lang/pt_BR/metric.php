<?php

return [
    'resource_label' => 'Métrica|Métricas',
    'list' => [
        'headers' => [
            'name' => 'Nome',
            'suffix' => 'Sufixo',
            'default_value' => 'Valor Padrão',
            'calc_type' => 'Tipo de Métrica',
            'display_chart' => 'Exibir Gráfico',
            'places' => 'Casas Decimais',
            'default_view' => 'Visualização Padrão',
            'threshold' => 'Limite',
            'order' => 'Ordem',
            'visible' => 'Visível',
            'points_count' => 'Contagem de Pontos',
            'created_at' => 'Criado em',
            'updated_at' => 'Atualizado em',
        ],
        'empty_state' => [
            'heading' => 'Métricas',
            'description' => 'Métricas são usadas para rastrear e exibir dados.',
        ],
    ],
    'form' => [
        'name_label' => 'Nome',
        'suffix_label' => 'Sufixo',
        'description_label' => 'Descrição',
        'default_view_label' => 'Visualização Padrão',
        'default_value_label' => 'Valor Padrão',
        'calc_type_label' => 'Tipo de Métrica',
        'places_label' => 'Casas Decimais',
        'threshold_label' => 'Limite',
        'visible_label' => 'Visível',
        'display_chart_label' => 'Exibir Gráfico',
    ],
    'overview' => [
        'metric_points_label' => 'Pontos de Métrica',
        'metric_points_description' => 'Pontos de métrica recentes.',
    ],
    'sum_label' => 'Soma',
    'average_label' => 'Média',
    'view_labels' => [
        'last_hour' => 'Última Hora',
        'today' => 'Hoje',
        'week' => 'Semana',
        'month' => 'Mês',
    ],
];
