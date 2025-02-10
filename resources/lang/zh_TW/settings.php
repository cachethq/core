<?php

return [
    'manage_cachet' => [
        'site_name_label' => '網站名稱',
        'about_this_site_label' => '關於此網站',
        'timezone_label' => '時區',
        'incident_days_label' => '事件天數',
        'major_outage_threshold_label' => '重大故障閾值',
        'refresh_rate_label' => '自動刷新頁面',
        'refresh_rate_label_input_suffix_seconds' => '秒',
        'recent_incidents_days_suffix_days' => '天',
        'toggles' => [
            'support_cachet' => '支持 Cachet',
            'show_timezone' => '顯示時區',
            'show_dashboard_link' => '顯示儀表板鏈接',
            'display_graphs' => '顯示圖表',
            'only_show_disrupted_days' => '僅顯示受干擾天數',
            'recent_incidents_only' => '僅顯示最近事件',
            'recent_incidents_days' => '顯示最近事件的天數',
        ],
    ],
    'manage_customization' => [
        'header_label' => '自定義 Header HTML',
        'footer_label' => '自定義 Footer HTML',
        'stylesheet_label' => '自定義 CSS',
    ],
    'manage_theme' => [
        'app_banner_label' => '橫幅圖像',
        'status_page_accent' => [
            'heading' => '狀態頁面強調色',
            'description' => '自定義狀態頁面的強調色。Cachet 可以自動選擇匹配的基礎色。',
            'accent_color_label' => '強調色',
            'accent_content_label' => '基礎色',
            'accent_pairing_label' => '強調色配對',
        ],
    ],
];
