<?php

return [
    'manage_cachet' => [
        'site_name_label' => '网站名称',
        'about_this_site_label' => '关于此网站',
        'timezone_label' => '时区',
        'timezone_other' => '其他',
        'browser_default' => '浏览器默认',
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
            'enable_external_dependencies' => '启用外部依赖',
            'recent_incidents_days' => '显示最近事件的天数',
        ],
        'display_settings_title' => '显示设置',
    ],
    'manage_customization' => [
        'header_label' => '自定义 Header HTML',
        'footer_label' => '自定义 Footer HTML',
        'stylesheet_label' => '自定义 CSS',
    ],
    'manage_localization' => [
        'locale_label' => '语言',
        'timezone_label' => '时区',
        'toggles' => [
            'show_timezone' => '在状态页面显示时区',
        ],
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
