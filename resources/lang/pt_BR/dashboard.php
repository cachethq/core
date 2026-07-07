<?php

return [
    'system_health' => [
        'empty' => 'Nenhum componente foi configurado ainda.',
        'add_component' => 'Adicionar um componente',
    ],
    'open_incidents' => [
        'heading' => 'Incidentes Abertos',
        'headers' => [
            'name' => 'Nome',
            'status' => 'Status',
            'occurred_at' => 'Ocorrido em',
            'components' => 'Componentes afetados',
        ],
        'actions' => [
            'create' => 'Criar Incidente',
        ],
        'empty_state' => [
            'heading' => 'Nenhum incidente aberto',
            'description' => 'Tudo certo. Incidentes que não foram resolvidos aparecerão aqui.',
        ],
    ],
    'upcoming_maintenance' => [
        'heading' => 'Manutenção',
        'headers' => [
            'name' => 'Nome',
            'status' => 'Status',
            'scheduled_at' => 'Agendado para',
        ],
        'actions' => [
            'create' => 'Agendar Manutenção',
        ],
        'empty_state' => [
            'heading' => 'Nenhuma manutenção agendada',
            'description' => 'Manutenções futuras e em andamento aparecerão aqui.',
        ],
    ],
];
