<?php

return [
    'resource_label' => 'Agendamento|Agendamentos',
    'list' => [
        'headers' => [
            'name' => 'Nome',
            'status' => 'Estado',
            'scheduled_at' => 'Agendado para',
            'completed_at' => 'Concluído em',
            'published_at' => 'Publicado em',
            'created_at' => 'Criado em',
            'updated_at' => 'Atualizado em',
            'deleted_at' => 'Eliminado em',
        ],
        'published_now' => 'Publicado',
        'filters' => [
            'scheduled' => 'Agendado (ainda não publicado)',
        ],
        'empty_state' => [
            'heading' => 'Agendamentos',
            'description' => 'Planeie e agende as suas manutenções.',
        ],
        'actions' => [
            'record_update' => 'Registar atualização',
            'complete' => 'Concluir manutenção',
        ],
    ],
    'form' => [
        'name_label' => 'Nome',
        'message_label' => 'Mensagem',
        'scheduled_at_label' => 'Agendado para',
        'scheduled_at_helper' => 'Quando começa a janela de manutenção.',
        'completed_at_label' => 'Concluído em',
        'completed_at_helper' => 'Quando se prevê que a janela de manutenção termine.',
        'published_at_label' => 'Publicado em',
        'published_at_helper' => 'Agende a manutenção para aparecer na página de estado numa data futura. Até lá, fica oculta para todos. Deixe em branco para publicar de imediato.',
        'notify_subscribers_label' => 'Notificar os subscritores sobre esta manutenção.',
        'notifications_helper' => 'Enviar e-mail aos subscritores sobre esta manutenção agendada e as respetivas atualizações.',
        'meta_label' => 'Metadados',
        'add_component' => [
            'action_label' => 'Adicionar componente',
            'header' => 'Componentes afetados',
            'component_label' => 'Componente',
            'status_label' => 'Estado durante a manutenção',
        ],
    ],
    'add_update' => [
        'new_update_label' => 'Nova atualização',
        'success_title' => 'Atualização registada',
        'success_body' => 'Publicou uma nova atualização em :name.',
        'form' => [
            'message_label' => 'Mensagem',
            'completed_at_label' => 'Concluído em',
        ],
    ],
    'status' => [
        'upcoming' => 'Agendada',
        'in_progress' => 'Em curso',
        'complete' => 'Concluída',
    ],
    'planned_maintenance_header' => 'Manutenção planeada',
];
