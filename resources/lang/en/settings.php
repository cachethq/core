<?php

return [
    'manage_cachet' => [
        'site_name_label' => 'Site Name',
        'about_this_site_label' => 'About This Site',
        'timezone_label' => 'Timezone',
        'timezone_other' => 'Other',
        'browser_default' => 'Browser Default',
        'incident_days_label' => 'Incident Days',
        'major_outage_threshold_label' => 'Major Outage Threshold',
        'refresh_rate_label' => 'Automatically Refresh Page',
        'refresh_rate_label_input_suffix_seconds' => 'seconds',
        'recent_incidents_days_suffix_days' => 'days',
        'toggles' => [
            'support_cachet' => 'Support Cachet',
            'show_dashboard_link' => 'Show Dashboard Link',
            'display_graphs' => 'Display Graphs',
            'enable_external_dependencies' => 'Enable External Dependencies',
            'only_show_disrupted_days' => 'Only Show Disrupted Days',
            'recent_incidents_only' => 'Show Recent Incidents Only',
            'recent_incidents_days' => 'Number of Days to Show Recent Incidents',
        ],
        'display_settings_title' => 'Display Settings',
    ],
    'manage_customization' => [
        'header_label' => 'Custom Header HTML',
        'footer_label' => 'Custom Footer HTML',
        'stylesheet_label' => 'Custom CSS',
    ],
    'manage_localization' => [
        'locale_label' => 'Locale',
        'timezone_label' => 'Timezone',
        'toggles' => [
            'show_timezone' => 'Show timezone on status page',
        ],
    ],
    'manage_theme' => [
        'app_banner_label' => 'Banner Image',
        'status_page_accent' => [
            'heading' => 'Status Page Accent',
            'description' => 'Customize the accent color of your status page. Cachet can automatically select a matching base color.',
            'accent_color_label' => 'Accent Color',
            'accent_content_label' => 'Base Color',
            'accent_pairing_label' => 'Accent Pairing',
        ],
    ],
];
