<?php

return [
    'resource_label' => 'Grupo de Componente|Grupos de Componentes',
    'incident_count' => ':count Incidente|:count Incidentes',
    'visibility' => [
        'expanded' => 'Sempre Expandido',
        'collapsed' => 'Sempre Recolhido',
        'collapsed_unless_incident' => 'Recolhido Exceto Durante Incidente',
    ],
    'list' => [
        'headers' => [
            'name' => 'Nome',
            'visible' => 'Visível',
            'collapsed' => 'Recolhido',
            'created_at' => 'Criado em',
            'updated_at' => 'Atualizado em',
        ],
        'empty_state' => [
            'heading' => 'Grupos de Componentes',
            'description' => 'Agrupar componentes relacionados.',
        ],
    ],
    'form' => [
        'name_label' => 'Nome',
        'visible_label' => 'Visível',
        'collapsed_label' => 'Recolhido',
    ],
];
