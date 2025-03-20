<?php

return [
    'resource_label' => 'Suscriptor|Suscriptores',
    'list' => [
        'headers' => [
            'email' => 'Correo Electrónico',
            'verify_code' => 'Código de Verificación',
            'global' => 'Global',
            'phone_number' => 'Número de Teléfono',
            'slack_webhook_url' => 'URL de Webhook de Slack',
            'verified_at' => 'Verificado el',
            'created_at' => 'Creado el',
            'updated_at' => 'Actualizado el',
        ],
        'empty_state' => [
            'heading' => 'Suscriptores',
            'description' => 'Los suscriptores son personas que se han suscrito a tu página de estado para recibir notificaciones.',
        ],
        'actions' => [
            'verify_label' => 'Verificar',
        ],
    ],
    'form' => [
        'email_label' => 'Correo Electrónico',
        'verify_code_label' => 'Código de Verificación',
        'verified_at_label' => 'Verificado el',
        'global_label' => 'Global',
    ],
    'overview' => [
        'total_subscribers_label' => 'Total de Suscriptores',
        'total_subscribers_description' => 'Número total de suscriptores.',
    ],
];
