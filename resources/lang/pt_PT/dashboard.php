<?php

return [
    'system_health' => [
        'empty' => 'Ainda não foram configurados componentes.',
        'add_component' => 'Adicionar um componente',
    ],
    'open_incidents' => [
        'heading' => 'Incidentes em aberto',
        'headers' => [
            'name' => 'Nome',
            'status' => 'Estado',
            'occurred_at' => 'Ocorrido',
            'components' => 'Componentes afetados',
        ],
        'actions' => [
            'create' => 'Criar incidente',
        ],
        'empty_state' => [
            'heading' => 'Não há incidentes em aberto',
            'description' => 'Está tudo bem. Os incidentes que ainda não foram resolvidos aparecem aqui.',
        ],
    ],
    'upcoming_maintenance' => [
        'heading' => 'Manutenção',
        'headers' => [
            'name' => 'Nome',
            'status' => 'Estado',
            'scheduled_at' => 'Agendada para',
        ],
        'actions' => [
            'create' => 'Agendar manutenção',
        ],
        'empty_state' => [
            'heading' => 'Não há manutenções agendadas',
            'description' => 'As janelas de manutenção futuras e em curso aparecem aqui.',
        ],
    ],
];
