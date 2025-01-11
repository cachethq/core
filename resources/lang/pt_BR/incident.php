<?php

return [
    'title' => 'Incidentes',
    'resource_label' => 'Incidente|Incidentes',
    'status' => [
        'investigating' => 'Investigando',
        'identified' => 'Identificado',
        'watching' => 'Monitorando',
        'fixed' => 'Resolvido',
        'reported' => 'Reportado',
    ],
    'edit_button' => 'Editar Incidente',
    'new_button' => 'Novo Incidente',
    'no_incidents_reported' => 'Nenhum incidente reportado.',
    'timeline' => [
        'past_incidents_header' => 'Incidentes Anteriores',
        'recent_incidents_header' => 'Incidentes Recentes',
        'no_incidents_reported_between' => 'Nenhum incidente reportado entre :from e :to',
        'navigate' => [
            'previous' => 'Anterior',
            'today' => 'Hoje',
            'next' => 'Próximo',
        ],
    ],
    'list' => [
        'headers' => [
            'name' => 'Nome',
            'status' => 'Status',
            'visible' => 'Visível',
            'stickied' => 'Fixado',
            'occurred_at' => 'Ocorrido em',
            'notified_subscribers' => 'Inscritos notificados',
            'created_at' => 'Criado em',
            'updated_at' => 'Atualizado em',
            'deleted_at' => 'Excluído em',
        ],
        'actions' => [
            'record_update' => 'Registrar Atualização',
            'view_incident' => 'Ver incidente',
        ],
        'empty_state' => [
            'heading' => 'Incidentes',
            'description' => 'Incidentes são usados para comunicar e rastrear o status dos seus serviços.',
        ],
    ],
    'form' => [
        'name_label' => 'Nome',
        'status_label' => 'Status',
        'message_label' => 'Mensagem',
        'occurred_at_label' => 'Ocorrido em',
        'occurred_at_helper' => 'O horário de criação do incidente será usado se deixado em branco.',
        'visible_label' => 'Visível',
        'user_label' => 'Usuário',
        'user_helper' => 'O usuário que reportou o incidente.',
        'notifications_label' => 'Notificar Inscritos?',
        'stickied_label' => 'Fixar Incidente?',
        'guid_label' => 'UUID do Incidente',
        'add_component' => [
            'action_label' => 'Adicionar Componente',
            'header' => 'Componentes',
            'component_label' => 'Componente',
            'status_label' => 'Status',
        ],
    ],
    'record_update' => [
        'success_title' => 'Incidente :name Atualizado',
        'success_body' => 'Uma nova atualização do incidente foi registrada.',
        'form' => [
            'message_label' => 'Mensagem',
            'status_label' => 'Status',
            'user_label' => 'Usuário',
            'user_helper' => 'Quem reportou este incidente.',
        ],
    ],
    'overview' => [
        'total_incidents_label' => 'Total de Incidentes',
        'total_incidents_description' => 'Número total de incidentes.',
    ],
];
