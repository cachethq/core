<?php

return [
    'resource_label' => 'Groupe de composants|Groupes de composants',
    'incident_count' => ':count Incident|:count Incidents',
    'visibility' => [
        'expanded' => 'Toujours développé',
        'collapsed' => 'Toujours réduit',
        'collapsed_unless_incident' => 'Réduit sauf en cas d’incident en cours',
    ],
    'list' => [
        'headers' => [
            'name' => 'Nom',
            'visible' => 'Visible',
            'collapsed' => 'Réduit',
            'created_at' => 'Créé le',
            'updated_at' => 'Mis à jour le',
        ],
        'empty_state' => [
            'heading' => 'Groupes de composants',
            'description' => 'Regroupez les composants liés ensemble.',
        ],
    ],
    'form' => [
        'name_label' => 'Nom',
        'visible_label' => 'Visible',
        'collapsed_label' => 'Réduit',
    ],
];
