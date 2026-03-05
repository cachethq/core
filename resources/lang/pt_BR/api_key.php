<?php

return [
    'resource_label' => 'Chave de API|Chaves de API',
    'show_token' => [
        'heading' => 'Seu Token de API foi gerado',
        'description' => 'Por favor, copie seu novo token de API. Por segurança, ele não será exibido novamente.',
        'copy_tooltip' => 'Token copiado!',
    ],
    'abilities_label' => ':ability :resource',
    'form' => [
        'name_label' => 'Nome do Token',
        'expires_at_label' => 'Expira Em',
        'expires_at_helper' => 'Expira à meia-noite. Deixe vazio para não expirar',
        'expires_at_validation' => 'A data de expiração deve ser no futuro',
        'abilities_label' => 'Permissões',
        'abilities_hint' => 'Deixar vazio dará ao token permissões completas',
    ],
    'list' => [
        'actions' => [
            'revoke' => 'Revogar',
        ],
        'headers' => [
            'name' => 'Nome do Token',
            'abilities' => 'Permissões',
            'created_at' => 'Criado Em',
            'expires_at' => 'Expira Em',
            'updated_at' => 'Atualizado Em',
        ],
    ],
];
