<?php

return [
    'resource_label' => 'Chave de API|Chaves de API',
    'show_token' => [
        'heading' => 'Copie o seu novo token de API',
        'description' => 'Por motivos de segurança, o token não voltará a ser mostrado.',
        'copy_tooltip' => 'Token copiado!',
    ],
    'abilities_label' => ':ability :resource',
    'form' => [
        'name_label' => 'Nome do token',
        'expires_at_label' => 'Expira em',
        'expires_at_helper' => 'Expira à meia-noite. Deixe em branco para não ter data de expiração.',
        'expires_at_validation' => 'A data de expiração tem de ser no futuro.',
        'abilities_label' => 'Permissões',
        'abilities_hint' => 'Deixe em branco para dar ao token todas as permissões.',
    ],
    'list' => [
        'actions' => [
            'revoke' => 'Revogar',
        ],
        'headers' => [
            'name' => 'Nome do token',
            'abilities' => 'Permissões',
            'created_at' => 'Criado em',
            'expires_at' => 'Expira em',
            'updated_at' => 'Atualizado em',
        ],
    ],
];
