<?php

return [
    'resource_label' => 'Komponent|Mga Komponent',
    'list' => [
        'headers' => [
            'name' => 'Pangalan',
            'status' => 'Kalagayan',
            'order' => 'Ayos',
            'group' => 'Grupo',
            'enabled' => 'Naka-enable',
            'created_at' => 'Ginawa Noong',
            'updated_at' => 'Na-update Noong',
            'deleted_at' => 'Nabura Noong',
        ],
        'empty_state' => [
            'heading' => 'Mga Komponent',
            'description' => 'Ang mga komponent ay kumakatawan sa iba’t ibang bahagi ng iyong sistema na maaaring makaapekto sa kalagayan ng iyong status page.',
        ],
    ],
    'last_updated' => 'Huling na-update noong :timestamp',
    'view_details' => 'Tingnan ang Detalye',
    'form' => [
        'name_label' => 'Pangalan',
        'status_label' => 'Kalagayan',
        'description_label' => 'Paglalarawan',
        'component_group_label' => 'Grupo ng Komponent',
        'link_label' => 'Link',
        'link_helper' => 'Isang opsyonal na link papunta sa komponent.',
    ],
    'status' => [
        'operational' => 'Gumagana',
        'performance_issues' => 'Mga Isyu sa Pagganap',
        'partial_outage' => 'Bahagyang Pagkaantala',
        'major_outage' => 'Malaking Pagkaantala',
        'unknown' => 'Hindi Alam',
    ],

];
