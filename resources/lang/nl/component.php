<?php

return [
    'resource_label' => 'Onderdeel|Componenten',
    'list' => [
        'headers' => [
            'name' => 'Naam',
            'status' => 'Toestand',
            'order' => 'Serie',
            'group' => 'Groep',
            'enabled' => 'Geactiveerd',
            'created_at' => 'Gemaakt op',
            'updated_at' => 'Bijgewerkt op',
            'deleted_at' => 'Verwijderd op',
        ],
        'empty_state' => [
            'heading' => 'Componenten',
            'description' => 'Componenten vertegenwoordigen de verschillende onderdelen van uw systeem die de status van uw statuspagina kunnen beïnvloeden.',
        ],
    ],
    'last_updated' => 'Laatst bijgewerkt :timestamp',
    'view_details' => 'Details weergeven',
    'form' => [
        'name_label' => 'Naam',
        'status_label' => 'Toestand',
        'description_label' => 'Beschrijving',
        'component_group_label' => 'Componentgroep',
        'link_label' => 'Link',
        'link_helper' => 'Een optionele link naar het onderdeel',
    ],
    'status' => [
        'operational' => 'Functioneel',
        'performance_issues' => 'Prestatieproblemen',
        'partial_outage' => 'Gedeeltelijke storing',
        'major_outage' => 'Ernstige storing',
        'unknown' => 'Onbekend',
    ],

];
