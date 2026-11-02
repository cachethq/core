<?php

return [
    'resource_label' => 'Componente|Componentes',
    'list' => [
        'headers' => [
            'name' => 'Nome',
            'status' => 'Estado',
            'order' => 'Ordem',
            'group' => 'Grupo',
            'enabled' => 'Ativo',
            'created_at' => 'Criado em',
            'checked' => 'Monitorizado',
            'checked_at' => 'Verificado em',
            'updated_at' => 'Atualizado em',
            'deleted_at' => 'Eliminado em',
        ],
        'empty_state' => [
            'heading' => 'Componentes',
            'description' => 'Os componentes representam as várias partes do seu sistema que podem afetar o estado da sua página de estado.',
        ],
        'ungrouped' => 'Componentes sem grupo',
    ],
    'attach' => [
        'heading' => 'Associar componentes',
        'placeholder' => 'Selecione os componentes',
    ],
    'last_updated' => 'Última atualização :timestamp',
    'description_label' => 'Sobre :component',
    'view_details' => 'Ver detalhes',
    'form' => [
        'name_label' => 'Nome',
        'status_label' => 'Estado',
        'description_label' => 'Descrição',
        'component_group_label' => 'Grupo de componentes',
        'link_label' => 'Ligação',
        'link_helper' => 'Uma ligação opcional para o componente.',
        'availability_section_title' => 'Disponibilidade',
        'enabled_label' => 'Ativo',
        'checked_label' => 'Monitorização',
        'checked_helper' => 'Verificar periodicamente este componente e registar a sua disponibilidade.',
    ],
    'status' => [
        'operational' => 'Operacional',
        'performance_issues' => 'Problemas de desempenho',
        'partial_outage' => 'Falha parcial',
        'major_outage' => 'Falha grave',
        'under_maintenance' => 'Em manutenção',
        'unknown' => 'Desconhecido',
    ],
    'status_source' => [
        'manual' => 'Manual',
        'monitor' => 'Monitorização',
        'import' => 'Importação',
        'system' => 'Sistema',
    ],
    'overview' => [
        'operational_components_label' => 'Componentes operacionais',
        'operational_components_description' => 'Componentes totalmente operacionais.',
    ],
    'checks' => [
        'title' => 'Verificações recentes',
        'empty_state' => [
            'heading' => 'Ainda não há verificações',
            'description' => 'As verificações aparecem aqui depois de o componente ser monitorizado.',
        ],
        'headers' => [
            'status' => 'Estado',
            'successful' => 'Com sucesso',
            'response_code' => 'Código de resposta',
            'response_time' => 'Tempo de resposta',
            'checked_at' => 'Verificado em',
        ],
    ],

];
