<?php

return [
    'resource_label' => 'API Key|Mga API Key',
    'show_token' => [
        'heading' => 'Nagawa na ang iyong API Token',
        'description' => 'Pakikopya ang bagong API token. Para sa iyong seguridad, hindi na ito makikita ulit.',
        'copy_tooltip' => 'Token nakopya!',
    ],
    'abilities_label' => ':ability :resource',
    'form' => [
        'name_label' => 'Pangalan ng Token',
        'expires_at_label' => 'Magtatapos Sa',
        'expires_at_helper' => 'Magtatapos sa hatinggabi. Iwanang walang laman kung walang expiration',
        'expires_at_validation' => 'Dapat nasa hinaharap ang petsa ng expiration',
        'abilities_label' => 'Mga Pahintulot',
        'abilities_hint' => 'Kung walang laman, magkakaroon ng buong pahintulot ang token',
    ],
    'list' => [
        'actions' => [
            'revoke' => 'Bawiin',
        ],
        'headers' => [
            'name' => 'Pangalan ng Token',
            'abilities' => 'Mga Pahintulot',
            'created_at' => 'Ginawa Noong',
            'expires_at' => 'Magtatapos Noong',
            'updated_at' => 'In-update Noong',
        ],
    ],
];
