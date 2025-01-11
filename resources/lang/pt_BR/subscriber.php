<?php

return [
    'resource_label' => 'Inscrito|Inscritos',
    'list' => [
        'headers' => [
            'email' => 'Email',
            'verify_code' => 'Código de verificação',
            'global' => 'Global',
            'phone_number' => 'Número de telefone',
            'slack_webhook_url' => 'URL do Webhook do Slack',
            'verified_at' => 'Verificado em',
            'created_at' => 'Criado em',
            'updated_at' => 'Atualizado em',
        ],
        'empty_state' => [
            'heading' => 'Inscritos',
            'description' => 'Inscritos são pessoas que se cadastraram na sua página de status para receber notificações.',
        ],
        'actions' => [
            'verify_label' => 'Verificar',
        ],
    ],
    'form' => [
        'email_label' => 'Email',
        'verify_code_label' => 'Código de verificação',
        'verified_at_label' => 'Verificado em',
        'global_label' => 'Global',
    ],
    'overview' => [
        'total_subscribers_label' => 'Total de Inscritos',
        'total_subscribers_description' => 'Número total de inscritos.',
    ],
];
