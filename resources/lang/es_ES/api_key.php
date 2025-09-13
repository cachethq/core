<?php

return [
    'resource_label' => 'Clave API|Claves API',
    'show_token' => [
        'heading' => 'Tu token de API ha sido generado',
        'description' => 'Por favor, copia tu nuevo token de API. Por seguridad, no se mostrará nuevamente.',
        'copy_tooltip' => '¡Token copiado!',
    ],
    'abilities_label' => ':ability :resource',
    'form' => [
        'name_label' => 'Nombre del Token',
        'expires_at_label' => 'Expira el',
        'expires_at_helper' => 'Expira a medianoche. Déjalo vacío si no quieres que expire',
        'expires_at_validation' => 'La fecha de expiración debe estar en el futuro',
        'abilities_label' => 'Permisos',
        'abilities_hint' => 'Si dejas esto vacío, el token tendrá permisos completos',
    ],
    'list' => [
        'actions' => [
            'revoke' => 'Revocar',
        ],
        'headers' => [
            'name' => 'Nombre del Token',
            'abilities' => 'Permisos',
            'created_at' => 'Creado el',
            'expires_at' => 'Expira el',
            'updated_at' => 'Actualizado el',
        ],
    ],
];
