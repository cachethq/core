<?php

return [
    'resource_label' => 'Grupo de Componentes|Grupos de Componentes',
    'incident_count' => ':count Incidente|:count incidentes',
    'visibility' => [
        'expanded' => 'Siempre Expandido',
        'collapsed' => 'Siempre Colapsado',
        'collapsed_unless_incident' => 'Colapsado salvo que haya un incidente en curso',
    ],
    'list' => [
        'headers' => [
            'name' => 'Nombre',
            'visible' => 'Visible',
            'collapsed' => 'Colapsado',
            'created_at' => 'Creado el',
            'updated_at' => 'Actualizado el',
        ],
        'empty_state' => [
            'heading' => 'Grupos de Componentes',
            'description' => 'Agrupa componentes relacionados entre sí.',
        ],
    ],
    'form' => [
        'name_label' => 'Nombre',
        'visible_label' => 'Visible',
        'collapsed_label' => 'Colapsado',
    ],
];
