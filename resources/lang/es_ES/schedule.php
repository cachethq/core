<?php

return [
    'resource_label' => 'Horario|Horarios',
    'list' => [
        'headers' => [
            'name' => 'Nombre',
            'status' => 'Estado',
            'scheduled_at' => 'Programado para',
            'completed_at' => 'Completado en',
            'created_at' => 'Creado el',
            'updated_at' => 'Actualizado el',
            'deleted_at' => 'Eliminado el',
        ],
        'empty_state' => [
            'heading' => 'Horarios',
            'description' => 'Planifica y programa tu mantenimiento.',
        ],
        'actions' => [
            'record_update' => 'Registrar Actualización',
            'complete' => 'Completar Mantenimiento',
        ],
    ],
    'form' => [
        'name_label' => 'Nombre',
        'message_label' => 'Mensaje',
        'scheduled_at_label' => 'Programado para',
        'completed_at_label' => 'Completado en',
    ],
    'add_update' => [
        'success_title' => 'Horario :name Actualizado',
        'success_body' => 'Se ha registrado una nueva actualización de horario.',
        'form' => [
            'message_label' => 'Mensaje',
            'completed_at_label' => 'Completado en',
        ],
    ],
    'status' => [
        'upcoming' => 'Próximo',
        'in_progress' => 'En progreso',
        'complete' => 'Completado',
    ],
    'planned_maintenance_header' => 'Mantenimiento Planificado',
];
