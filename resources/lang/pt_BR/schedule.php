<?php

return [
    'resource_label' => 'Agendamento|Agendamentos',
    'list' => [
        'headers' => [
            'name' => 'Nome',
            'status' => 'Status',
            'scheduled_at' => 'Agendado para',
            'completed_at' => 'Concluído em',
            'created_at' => 'Criado em',
            'updated_at' => 'Atualizado em',
            'deleted_at' => 'Excluído em',
        ],
        'empty_state' => [
            'heading' => 'Agendamentos',
            'description' => 'Planeje e agende suas manutenções.',
        ],
        'actions' => [
            'record_update' => 'Registrar Atualização',
            'complete' => 'Concluir Manutenção',
        ],
    ],
    'form' => [
        'name_label' => 'Nome',
        'message_label' => 'Mensagem',
        'scheduled_at_label' => 'Agendado para',
        'completed_at_label' => 'Concluído em',
    ],
    'add_update' => [
        'success_title' => 'Agendamento :name Atualizado',
        'success_body' => 'Uma nova atualização do agendamento foi registrada.',
        'form' => [
            'message_label' => 'Mensagem',
            'completed_at_label' => 'Concluído em',
        ],
    ],
    'status' => [
        'upcoming' => 'Próximo',
        'in_progress' => 'Em Andamento',
        'complete' => 'Concluído',
    ],
    'planned_maintenance_header' => 'Manutenção Planejada',
];
