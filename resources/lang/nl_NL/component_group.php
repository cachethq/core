<?php

return [
    'resource_label' => 'Componentgroep|Componentgroepen',
    'incident_count' => ':count Incident|:count Incidenten',
    'visibility' => [
        'expanded' => 'Altijd ontvouwd',
        'collapsed' => 'Altijd gevouwen',
        'collapsed_unless_incident' => 'Gevouwen - behalve tijdens een actief incident',
    ],
    'list' => [
        'headers' => [
            'name' => 'Naam',
            'visible' => 'Zichtbaar',
            'collapsed' => 'Uitgevouwen',
            'created_at' => 'Gemaakt op',
            'updated_at' => 'Bijgewerkt op',
        ],
        'empty_state' => [
            'heading' => 'Componentgroepen',
            'description' => 'Groepsgerelateerde componenten',
        ],
    ],
    'form' => [
        'name_label' => 'Naam',
        'visible_label' => 'Zichtbaar',
        'collapsed_label' => 'Uitgevouwen',
    ],
];
