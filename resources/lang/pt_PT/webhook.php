<?php

return [
    'resource_label' => 'Webhook|Webhooks',
    'event_selection' => [
        'all' => 'Enviar todos os eventos',
        'selected' => 'Enviar apenas os eventos selecionados',
    ],
    'form' => [
        'url_label' => 'URL de destino',
        'url_helper' => 'Os eventos são enviados por POST para este URL.',
        'secret_label' => 'Segredo',
        'secret_helper' => 'O conteúdo é assinado com este segredo. Consulte a *documentação dos webhooks* para mais informações.',
        'description_label' => 'Descrição',
        'events_to_send_label' => 'Eventos a enviar',
        'events_label' => 'Eventos',
        'edit_secret_label' => 'Editar segredo',
        'update_secret_label' => 'Atualizar segredo',
    ],
    'attempts' => [
        'heading' => 'Tentativas',
        'empty_state' => 'Ainda não houve tentativas de envio para este webhook.',
    ],
    'list' => [
        'headers' => [
            'url' => 'URL de destino',
            'success_rate_24h' => 'Taxa de sucesso (24 h)',
        ],
    ],
];
