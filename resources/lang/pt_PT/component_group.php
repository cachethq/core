<?php

return [
    'resource_label' => 'Grupo de componentes|Grupos de componentes',
    'incident_count' => ':count incidente|:count incidentes',
    'visibility' => [
        'expanded' => 'Sempre expandido',
        'collapsed' => 'Sempre fechado',
        'collapsed_unless_incident' => 'Fechado, exceto com incidentes em curso',
    ],
    'list' => [
        'headers' => [
            'name' => 'Nome',
            'visible' => 'Visível',
            'collapsed' => 'Fechado',
            'order_column' => 'Ordem do grupo de componentes',
            'created_at' => 'Criado em',
            'updated_at' => 'Atualizado em',
        ],
        'empty_state' => [
            'heading' => 'Grupos de componentes',
            'description' => 'Agrupe componentes relacionados.',
        ],
    ],
    'form' => [
        'name_label' => 'Nome',
        'visible_label' => 'Visível',
        'collapsed_label' => 'Fechado',
        'order_column_label' => 'Ordem do grupo de componentes',
        'order_direction' => 'Sentido da ordenação',
        'meta_label' => 'Metadados',
    ],
];
