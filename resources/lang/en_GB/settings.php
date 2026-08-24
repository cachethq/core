<?php

return [
    'manage_cachet' => [
        'toggles' => [
            'show_site_name' => 'Show site name',
            'show_site_name_helper' => 'Display the site name on the public status page.',
            'show_about' => 'Show About this site',
            'show_about_helper' => 'Display the About this site content on the public status page.',
            'show_component_group_status' => 'Show component group status',
            'show_component_group_status_helper' => 'Display each group’s most severe component status on the public status page.',
        ],
    ],
    'manage_theme' => [
        'status_page_accent' => [
            'description' => 'Customise the accent colour of your status page. Cachet can automatically select a matching base colour.',
            'accent_color_label' => 'Accent colour',
            'accent_color_helper' => 'Primary colour used for headings, links, and active states.',
            'accent_content_label' => 'Base colour',
            'accent_content_helper' => 'Neutral base colour paired with the accent. Disabled when accent pairing is on.',
            'accent_pairing_helper' => 'Automatically pick a base colour that complements the accent.',
        ],
    ],
];
