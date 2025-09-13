<?php

return [
    'resource_label' => 'Webhook|Webhooks',
    'event_selection' => [
        'all' => 'Enviar todos los eventos',
        'selected' => 'Enviar solo los eventos seleccionados',
    ],
    'form' => [
        'url_label' => 'URL de Payload',
        'url_helper' => 'Los eventos se enviarán mediante POST a esta URL.',
        'secret_label' => 'Secreto',
        'secret_helper' => 'El payload será firmado con este secreto. Consulta la *documentación del webhook* para más información.',
        'description_label' => 'Descripción',
        'event_selection_label' => '¿Enviar todos los eventos?',
        'events_label' => 'Eventos',
        'edit_secret_label' => 'Editar secreto',
        'update_secret_label' => 'Actualizar secreto',
    ],
    'attempts' => [
        'heading' => 'Intentos',
        'empty_state' => 'Aún no se han realizado intentos en este webhook',
    ],
    'list' => [
        'headers' => [
            'url' => 'URL de Payload',
            'success_rate_24h' => 'Tasa de éxito (24h)',
        ],
    ],
];
