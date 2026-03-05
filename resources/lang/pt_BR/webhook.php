<?php

return [
    'resource_label' => 'Webhook|Webhooks',
    'event_selection' => [
        'all' => 'Enviar todos os eventos',
        'selected' => 'Enviar apenas eventos selecionados',
    ],
    'form' => [
        'url_label' => 'URL de Payload',
        'url_helper' => 'Os eventos serão enviados via POST para esta URL.',
        'secret_label' => 'Segredo',
        'secret_helper' => 'O payload será assinado com este segredo. Consulte a *documentação de webhooks* para mais informações.',
        'description_label' => 'Descrição',
        'event_selection_label' => 'Enviar todos os eventos?',
        'events_label' => 'Eventos',
        'edit_secret_label' => 'Editar segredo',
        'update_secret_label' => 'Atualizar segredo',
    ],
    'attempts' => [
        'heading' => 'Tentativas',
        'empty_state' => 'Nenhuma tentativa foi feita para este webhook ainda',
    ],
    'list' => [
        'headers' => [
            'url' => 'URL de Payload',
            'success_rate_24h' => 'Taxa de sucesso (24h)',
        ],
    ],
];
