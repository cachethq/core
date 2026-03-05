<?php

return [
    'manage_cachet' => [
        'site_name_label' => 'Nome do Site',
        'about_this_site_label' => 'Sobre Este Site',
        'timezone_label' => 'Fuso Horário',
        'timezone_other' => 'Outro',
        'browser_default' => 'Padrão do navegador',
        'incident_days_label' => 'Dias de Incidentes',
        'major_outage_threshold_label' => 'Limite de Indisponibilidade Total',
        'refresh_rate_label' => 'Atualizar Página Automaticamente',
        'refresh_rate_label_input_suffix_seconds' => 'segundos',
        'recent_incidents_days_suffix_days' => 'dias',
        'toggles' => [
            'support_cachet' => 'Apoiar o Cachet',
            'show_timezone' => 'Exibir Fuso Horário',
            'show_dashboard_link' => 'Exibir Link do Painel',
            'display_graphs' => 'Exibir Gráficos',
            'only_show_disrupted_days' => 'Exibir Apenas Dias com Interrupções',
            'recent_incidents_only' => 'Exibir Apenas Incidentes Recentes',
            'enable_external_dependencies' => 'Habilitar Dependências Externas',
            'recent_incidents_days' => 'Número de Dias para Exibir Incidentes Recentes',
        ],
        'display_settings_title' => 'Configurações de Exibição',
    ],
    'manage_customization' => [
        'header_label' => 'HTML Personalizado do Cabeçalho',
        'footer_label' => 'HTML Personalizado do Rodapé',
        'stylesheet_label' => 'CSS Personalizado',
    ],
    'manage_localization' => [
        'locale_label' => 'Idioma',
        'timezone_label' => 'Fuso horário',
        'toggles' => [
            'show_timezone' => 'Exibir fuso horário na página de status',
        ],
    ],
    'manage_theme' => [
        'app_banner_label' => 'Imagem do Banner',
        'status_page_accent' => [
            'heading' => 'Cor de Destaque da Página de Status',
            'description' => 'Personalize a cor de destaque da sua página de status. O Cachet pode selecionar automaticamente uma cor base correspondente.',
            'accent_color_label' => 'Cor de Destaque',
            'accent_content_label' => 'Cor Base',
            'accent_pairing_label' => 'Combinação de Destaque',
        ],
    ],
];
