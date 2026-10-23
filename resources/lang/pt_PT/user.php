<?php

return [
    'resource_label' => 'Utilizador|Utilizadores',
    'profile_information_title' => 'Informações do perfil',
    'security_section_title' => 'Palavra-passe e segurança',
    'list' => [
        'headers' => [
            'name' => 'Nome',
            'email' => 'Endereço de e-mail',
            'email_verified_at' => 'E-mail verificado em',
            'is_admin' => 'Administrador',
        ],
        'actions' => [
            'verify_email' => 'Verificar e-mail',
            'reset_two_factor' => 'Repor autenticação de dois fatores',
            'reset_two_factor_confirmation' => 'Isto remove a configuração de autenticação de dois fatores e os códigos de recuperação do utilizador. Até voltar a configurá-la, poderá iniciar sessão apenas com a palavra-passe.',
            'reset_two_factor_success' => 'Autenticação de dois fatores reposta.',
        ],
    ],
    'form' => [
        'name_label' => 'Nome',
        'email_label' => 'Endereço de e-mail',
        'password_label' => 'Palavra-passe',
        'password_confirmation_label' => 'Confirmar palavra-passe',
        'preferred_locale' => 'Idioma preferido',
        'preferred_locale_system_default' => 'Predefinição do sistema',
        'is_admin_label' => 'Administrador',
    ],
];
