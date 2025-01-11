<?php

return [
    'resource_label' => 'Componente|Componentes',
    'list' => [
        'headers' => [
            'name' => 'Nome',
            'status' => 'Status',
            'order' => 'Ordem',
            'group' => 'Grupo',
            'enabled' => 'Ativo',
            'created_at' => 'Criado em',
            'updated_at' => 'Atualizado em',
            'deleted_at' => 'Excluído em',
        ],
        'empty_state' => [
            'heading' => 'Componentes',
            'description' => 'Os componentes representam as várias partes do seu sistema que podem afetar o status da sua página de status.',
        ],
    ],
    'last_updated' => 'Última atualização :timestamp',
    'view_details' => 'Ver Detalhes',
    'form' => [
        'name_label' => 'Nome',
        'status_label' => 'Status',
        'description_label' => 'Descrição',
        'component_group_label' => 'Grupo de Componentes',
        'link_label' => 'Link',
        'link_helper' => 'Um link opcional para o componente.',
    ],
    'status' => [
        'operational' => 'Operacional',
        'performance_issues' => 'Problemas de Performance',
        'partial_outage' => 'Indisponibilidade Parcial',
        'major_outage' => 'Indisponibilidade Total',
        'unknown' => 'Desconhecido',
    ],
];
