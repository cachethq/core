<?php

return [
    'system_health' => [
        'empty' => 'Aún no se ha configurado ningún componente.',
        'add_component' => 'Añadir un componente',
    ],
    'open_incidents' => [
        'heading' => 'Incidentes Abiertos',
        'headers' => [
            'name' => 'Nombre',
            'status' => 'Estado',
            'occurred_at' => 'Ocurrido el',
            'components' => 'Componentes afectados',
        ],
        'actions' => [
            'create' => 'Crear Incidente',
        ],
        'empty_state' => [
            'heading' => 'No hay incidentes abiertos',
            'description' => 'Todo en orden. Los incidentes que no hayan sido solucionados aparecerán aquí.',
        ],
    ],
    'upcoming_maintenance' => [
        'heading' => 'Mantenimiento',
        'headers' => [
            'name' => 'Nombre',
            'status' => 'Estado',
            'scheduled_at' => 'Programado para',
        ],
        'actions' => [
            'create' => 'Programar Mantenimiento',
        ],
        'empty_state' => [
            'heading' => 'No hay mantenimientos programados',
            'description' => 'Los mantenimientos próximos y en progreso aparecerán aquí.',
        ],
    ],
];
