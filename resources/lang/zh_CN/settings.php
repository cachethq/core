<?php

return [
    'manage_cachet' => [
        'site_name_label' => '网站名称',
        'about_this_site_label' => '关于此网站',
        'timezone_label' => '时区',
        'incident_days_label' => '事件天数',
        'major_outage_threshold_label' => '重大故障阈值',
        'refresh_rate_label' => '自动刷新页面',
        'refresh_rate_label_input_suffix_seconds' => '秒',
        'recent_incidents_days_suffix_days' => '天',
        'toggles' => [
            'support_cachet' => '支持 Cachet',
            'show_timezone' => '显示时区',
            'show_dashboard_link' => '显示仪表板链接',
            'display_graphs' => '显示图表',
            'only_show_disrupted_days' => '仅显示受干扰天数',
            'recent_incidents_only' => '仅显示最近事件',
            'recent_incidents_days' => '显示最近事件的天数',
        ],
    ],
    'manage_customization' => [
        'header_label' => '自定义 Header HTML',
        'footer_label' => '自定义 Footer HTML',
        'stylesheet_label' => '自定义 CSS',
    ],
    'manage_theme' => [
        'app_banner_label' => '横幅图像',
        'status_page_accent' => [
            'heading' => '状态页面强调色',
            'description' => '自定义状态页面的强调色。Cachet 可以自动选择匹配的基础色。',
            'accent_color_label' => '强调色',
            'accent_content_label' => '基础色',
            'accent_pairing_label' => '强调色配对',
        ],
    ],
];
