<?php

return [
    'resource_label' => 'Componente|Componentes',
    'list' => [
        'headers' => [
            'name' => 'Nombre',
            'status' => 'Estado',
            'order' => 'Orden',
            'group' => 'Grupo',
            'enabled' => 'Habilitado',
            'created_at' => 'Creado el',
            'updated_at' => 'Actualizado el',
            'deleted_at' => 'Eliminado el',
        ],
        'empty_state' => [
            'heading' => 'Componentes',
            'description' => 'Los componentes representan las diversas partes de tu sistema que pueden afectar el estado de tu página de estado.',
        ],
    ],
    'last_updated' => 'Última actualización :timestamp',
    'view_details' => 'Ver detalles',
    'form' => [
        'name_label' => 'Nombre',
        'status_label' => 'Estado',
        'description_label' => 'Descripción',
        'component_group_label' => 'Grupo de Componentes',
        'link_label' => 'Enlace',
        'link_helper' => 'Un enlace opcional al componente.',
    ],
    'status' => [
        'operational' => 'Operativo',
        'performance_issues' => 'Problemas de rendimiento',
        'partial_outage' => 'Interrupción parcial',
        'major_outage' => 'Interrupción mayor',
        'unknown' => 'Desconocido',
    ],
];
